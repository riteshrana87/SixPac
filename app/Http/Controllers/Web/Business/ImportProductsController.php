<?php

namespace App\Http\Controllers\Web\Business;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Products;
use App\Models\ProductCategory;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
//use ZanySoft\Zip\Zip;
use Intervention\Image\Facades\Image;
use Zip;
use File;
use getID3;
use Illuminate\Support\Facades\Session;

class ImportProductsController extends Controller
{
	public function __construct(){
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->fileSystemCloud	            = Config::get('constant.FILESYSTEM_CLOUD');

        $this->productOriginalImagePath	    = Config::get('constant.PRODUCTS_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->productThumbImagePath	    = Config::get('constant.PRODUCTS_THUMB_PHOTO_UPLOAD_PATH');

        $this->productImportPath		    = Config::get('constant.IMPORT_PRODUCTS_CSV_UPLOAD_PATH');
        $this->productZipImportPath		    = Config::get('constant.IMPORT_PRODUCTS_ZIP_UPLOAD_PATH');
		$this->productDownloadSamplePath    = Config::get('constant.DOWNLOAD_PRODUCTS_CSV_SAMPLE_UPLOAD_PATH');

        $this->productOrgImageWidth     = Config::get('constant.PRODUCTS_ORG_PHOTO_WIDTH');
        $this->productOrgImageHeight    = Config::get('constant.PRODUCTS_ORG_PHOTO_HEIGHT');
        $this->productThumbImageWidth	= Config::get('constant.PRODUCTS_THUMB_PHOTO_WIDTH');
        $this->productThumbImageHeight	= Config::get('constant.PRODUCTS_THUMB_PHOTO_HEIGHT');
    }

    /*
        @Author : Spec Developer
        @Desc   : Import products page.
        @Date   : 04/04/2022
    */

    public function importProducts(){
        $data['page_title'] = 'Import Products';
        $data['page_js']    = array(
            'backend/assets/business/js/import_products.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(

        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'Products.importProducts();'
        );

		$data['sampleFilePath']	= Storage::disk($this->fileSystemCloud)->url($this->productDownloadSamplePath.'sample_products.csv');

		return view('business.products.import',$data);
    }

    /**
     * abortImport is used to abort import process
     *
     * @param  Request $request request parameter
     * @return array   output array
     * @author Spec Developer
     */
    public function abortImport($request)
    {
   		$output = [];
        $index = $request->input('index_key');
        $productIds = $request->input('inserted_id');
        $originalStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath);
	    $thumbStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath);
        $expProdIdsArr = explode(',', $productIds);
        if (count($expProdIdsArr) > 0) {
            for ($pro = 0; $pro < count($expProdIdsArr); $pro++) {
                deleteProductAndMedia($expProdIdsArr[$pro], $originalStoragePath, $thumbStoragePath, 1);
            }
        }
        $output = array(
			'msg_type'		=> 'success',
			'msg' 			=> 'Product Import cancelled successfully!',
			'row_number'	=> $index,
			'product_id' 	=> 0,
			'sku_id' 		=> "",
			'is_exists' 	=> "",
		);
    	return $output;
    }

    /**
     * skipImpoprt is used to skip csv row to import
     *
     * @param  Request $request request parameter
     * @return array   output array
     * @author Spec Developer
     */
   public function skipImpoprt($request)
   {
   	 	$index = $request->input('index_key');
   	 	$productId = $request->input('old_product_id');
   	 	$proRs = Products::where('id',$productId)->select('id', 'sku')->withTrashed()->first();
   	 	$sku = $proRs->sku ?? null;
   	 	$is_exists = 1;
	   	$output = array(
			    'msg_type'      => 'success',
			    'msg'           => 'Row number = '.$index.' added successfully!',
			    'row_number'    => $index,
			    'product_id'   	=> $productId,
			    'sku_id'        => trim($sku),
				'is_exists' 	=> $is_exists,
		);
		return $output;
   }

   /**
    * readCSVData is used to read data from given csv path
    *
    * @param  string $csv_path csv path
    * @return array  csv data array
    * @author Spec Developer
    */
    public function readCSVData($csv_path)
    {
        $importData_arr = array();
        if (empty($csv_path)) {
        	return $importData_arr;
        }
   		$file_data = fopen($csv_path, 'r');
        $i = 0;
        while (($filedata = fgetcsv($file_data, 1000, ",")) !== FALSE) {
            $num = count($filedata );
            // Skip first row (Remove below comment if you want to skip the first row)
            if ($i == 0) {
                $i++;
                continue;
            }
            for ($c=0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata [$c];
            }
            $i++;
        }
        return $importData_arr;
    }

   /**
    * createOrUpdateProduct is used to create or update product
    *
    * @param  array  $importData   import data array
    * @param  int    $productCatId category primary id
    * @param  string $operation    operation that you want to perform
    * @return mixed  return product id or product array
    * @author Spec Developer
    */
    public function createOrUpdateProduct($importData, $productCatId, $operation)
    {
   		if (empty($importData) || empty($productCatId)) {
   			return false;
   		}
		$input['product_title'] =   substr(trim($importData[0]), 0, 70);
		$input['product_description']   =   substr(trim($importData[2]), 0, 255);
		$input['category_id']   =	$productCatId;
		$input['sku']       	=	trim($importData[3]);
		$input['quantity']  	=	trim($importData[4]);
		$input['cost_price']	=	trim($importData[5]);
		$input['sell_price']	=	trim($importData[6]);
		$input['user_id']   = 	Auth::user()->id;
		$input['status']    =	1;
		if ($operation=='update') {
			return Products::where('sku', $importData[3])->update($input);
		} else if ($operation=='create') {
			return Products::create($input);
		}
   }

   /**
    * getProductAndCategory is used to ger product and category data
    *
    * @param  array $importData csv data array
    * @return array product data array
    * @author Spec Developer
    */
    public function getProductAndCategory($importData)
    {
   		$result = ['product'=>'', 'productCatId'=>''];
   		if (empty($importData)) {
   			return $result;
   		}
   		$proRs = Products::where('sku', $importData[3])->withTrashed()->first();
        $categoryName = strtolower(trim($importData[1]));
        $proCatRs = '';
        $productCatId = NULL;
        if (!empty($categoryName)) {
            $proCatRs = ProductCategory::select('id')->whereRaw("LOWER(category_name) = ?", [$categoryName])->first();
            if (empty($proCatRs)) {
                $obj = new ProductCategory([
                    "category_name"	=>  substr(trim($importData[1]), 0, 70),
                    "created_by"   	=>  Auth::user()->id,
                    "status"    	=>  1,
                ]);
                $obj->save();
                $productCatId = $obj->id;
            } else {
                $productCatId = $proCatRs->id;
            }
        }
        $result = ['product'=>$proRs, 'productCatId'=>$productCatId];
        return $result;
    }

   /**
    * importMedia is used to import media like images and video
    *
    * @param  Request $request    request parameter
    * @param  array   $importData csv data array
    * @param  int     $productId  product primary array
    * @param  int 	  $x          import index
    * @return bool    true if media imported
    * @author Spec Developer
    */
    public function importMedia($request, $importData, $productId, $x)
    {
   		$proImages[$x] = trim($importData[7]);
   		$gallery_folder_path = $request->input('gallery_folder');
   		$total_rows = $request->input('total_rows');
        $index    	= $request->input('index_key');
        $action = $request->input('action');
        $error = $is_exists = $isvalidMedia = 1;
        $originalStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath);
	    $thumbStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath);
        if (count($proImages) > 0 && is_dir($gallery_folder_path) && !empty($proImages[$x])) {
			deleteProductAndMedia($productId, $originalStoragePath, $thumbStoragePath, 0);
            $zipFolderName = basename($gallery_folder_path);
            foreach ($proImages as $imgArr) {
                $images = explode(',',$imgArr);
                if (count($images) > 0) {
                    if (count($images) > 5) {
                        $error = 1;
                        $output = array(
                            'msg_type'      => 'error',
                            'msg'           => trans('admin-message.PRODUCT_MEDIA_FILE_LIMIT_ERROR'),
                            'row_number'    => $index,
                            'product_id'   => $productId,
                            'sku_id'        => trim($importData[3]),
							'is_exists' 	=> $is_exists,
                        );

                        deleteProductAndMedia($productId, $originalStoragePath, $thumbStoragePath, 0);
                        break;
                    } else {
			            foreach ($images as $key => $galleryArr) {
							$mediaName = trim($galleryArr);
							if (!empty($mediaName)) {
								$ext = strtolower(pathinfo($mediaName, PATHINFO_EXTENSION));
								// $newFileName = Str::random(20) . '.' . $ext;
								$fileIndex = empty($isvalidMedia) ? $key : ($key+1);
								$fileIndex = empty($fileIndex) ? 1 : $fileIndex;
								$newFileName = $fileIndex.'_'.$productId.'.'.$ext;
                    			$thumbFileName = $fileIndex.'_'.$productId.'_thumb.'.$ext;
                    			$thumbName = $thumbFileName;
								// Check extracted file size on extension basis.
								if (file_exists($gallery_folder_path.'/'.$mediaName)) {
									$mediaFileSize = filesize($gallery_folder_path.'/'.$mediaName);
									$file_length = 0;

									if (in_array($ext, Config::get('constant.IMAGE_EXTENSION'))) {
										if ($mediaFileSize <= 307200 ) {
											// Check if media image file size is less than or equal to 300 KB = 307200 bytes(in binary)
											$org_img = Image::make($gallery_folder_path.'/'.$mediaName);
											$org_img->resize($this->productOrgImageWidth, $this->productOrgImageHeight, function ($constraint) {
												$constraint->aspectRatio();
											})->save($originalStoragePath.$newFileName);

											$org_img = Image::make($originalStoragePath.'/'.$newFileName);
											$org_img->resize($this->productThumbImageWidth, $this->productThumbImageHeight, function ($constraint) {
												$constraint->aspectRatio();
											})->save($thumbStoragePath.$thumbFileName);

										} else {
												$error = 1;
												$output = array(
													'msg_type'      => 'error',
													'msg'           => trans('admin-message.PRODUCT_IMAGE_SIZE_LIMIT_ERROR'),
													'row_number'    => $index,
													'product_id'   	=> $productId,
													'sku_id'        => trim($importData[3]),
													'is_exists' 	=> $is_exists,
												);

												// deleteProductAndMedia($productId, $originalStoragePath, $thumbStoragePath, 0);
												$isvalidMedia = 0;
												continue;
										}
									}

									if (in_array($ext, Config::get('constant.VIDEO_EXTENSION'))) {
										if ($mediaFileSize <= 1048576 ) {
											// Check if media image file size is less than or equal to 1MB = 1048576 bytes (in binary)
											File::move($gallery_folder_path.'/'.$mediaName, $originalStoragePath.$newFileName);

											$getID3 = new getID3;
											$videoPath = $originalStoragePath.$newFileName;
											$thumbPath = $thumbStoragePath.$fileIndex.'_'.$productId.'_thumb.jpg';
											$video_file = $getID3->analyze($videoPath);
											$file_length = $video_file['playtime_seconds']; // Get the duration in seconds
											$thumbName = getVideoThumb($videoPath, $thumbPath, $file_length);
										} else {
											$error = 1;
											$output = array(
												'msg_type'      => 'error',
												'msg'           => trans('admin-message.PRODUCT_VIDEO_SIZE_LIMIT_ERROR'),
												'row_number'    => $index,
												'product_id'   	=> $productId,
												'sku_id'        => trim($importData[3]),
												'is_exists' 	=> $is_exists,
											);

											// Create function for delete product gallery and product

											// deleteProductAndMedia($productId, $originalStoragePath, $thumbStoragePath, 0);
											$isvalidMedia = 0;
											continue;
										}
									}
									// Store files into product gallery table
									$objGallery = new ProductGallery([
										"product_id"        =>  $productId,
										"file_name"         =>  $newFileName,
										"file_type"         =>  $ext,
										"thumb_name"	    =>  $thumbName,
										"file_length"       =>  $file_length,
										"file_size"         =>  $mediaFileSize,
										"is_transacoded"    => 'transacoded',
										"user_id"           =>  Auth::user()->id,
										"status"            =>  1,
									]);
									$objGallery->save();
								} else {
									$error = 1;
									$output = array(
										'msg_type'      => 'error',
										'msg'           => trans('admin-message.PRODUCT_MEDIA_NOT_FOUND'),
										'row_number'    => $index,
										'product_id'   	=> $productId,
										'sku_id'        => trim($importData[3]),
										'is_exists' 	=> $is_exists,
									);
									// Create function for delete product gallery and product
									// deleteProductAndMedia($productId, $originalStoragePath, $thumbStoragePath, 0);

									if (($total_rows == ((int)$index + (int) 1))) {
										if (file_exists($csv_path)) {
											@unlink($csv_path);
										}
										if (file_exists($gallery_folder_path.".zip")) {
											@unlink($gallery_folder_path.".zip");
										}

										if (file_exists($gallery_folder_path)) {
											//@unlink($gallery_folder_path);
											File::deleteDirectory($gallery_folder_path);
										}
									}
									break;
								}
							}
						}
                    }
                }
            }
        }
        if (($total_rows == $index) || ($action == 'abort')) {
            if (file_exists($csv_path)) {
                @unlink($csv_path);
            }
            if (file_exists($gallery_folder_path.".zip")) {
                @unlink($gallery_folder_path.".zip");
            }

            if (file_exists($gallery_folder_path)) {
                //@unlink($gallery_folder_path);
                File::deleteDirectory($gallery_folder_path);
            }
        }
        return true;
    }

    /**
     * overrideImport is used to override imported data
     *
     * @param  Request $request request parameter
     * @param  array   $input   input parameter
     * @return array   success or error data array
     * @author Spec Developer
     */
	public function overrideImport($request, $input)
	{
		$output = [];
		$is_exists = 0;
	    $total_rows = $input['total_rows'];
   		try {
	   		$error = 0;
	        $csv_path = $input['csv_path'];
	        $gallery_folder_path = $input['gallery_folder'];
			$action		= $input['action'];
	        $indexArr    	= $input['index_key'];
	        $productIdsArr = $input['product_ids'];
	        $oldProductIdArr = $input['old_product_id'];
	        $zipFolderName = $msgType = $msg = '';
	        $originalStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath);
	        $thumbStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath);
	        if (empty($indexArr)) {
	        	throw new \Exception("Unable to import CSV");
	        }
	   		if ($csv_path) {
	            $importData_arr = $this->readCSVData($csv_path);
	            if (empty($importData_arr)) {
		        	throw new \Exception("Unable to import CSV");
		        }
	         	// Insert to MySQL database
			    $x = 0;
			    foreach ($importData_arr as $key => $importData) {
			    	if (in_array($x, $indexArr)) {
			    		$index = $x;
			            $input = array();
			            $productData = $this->getProductAndCategory($importData);
			            $proRs = $productData['product'] ?? [];
			            $productCatId = $productData['productCatId'] ?? null;
						if (!empty($proRs)) {
							// Update existing record
		                    $productId = $proRs->id;
		                    $is_exists = 1;
							$this->createOrUpdateProduct($importData, $productCatId, 'update');
						} else {
							$product = $this->createOrUpdateProduct($importData, $productCatId, 'create');
							$productId = $product->id;
		                    $is_exists = 0;
						}
						$this->importMedia($request, $importData, $productId, $x);
						$msgType = 'success';
						$msg = 'CSV imported successfully!';
					}
		        	$x++;
			    }
	        }
	    } catch (\Exception $e) {
	    	$error = 1;
	    	$msgType = 'error';
			$msg = $e->getMessage();
	    }
    	$output = array(
                'msg_type'      => $msgType,
                'msg'           => $msg,
                'row_number'    => empty($error) ? ($total_rows-1) : 0,
                'product_id'   	=> "",
                'sku_id'        => "",
				'is_exists' 	=> $is_exists,
				'total_rows'	=> $total_rows,
            );
        return $output;
    }

   	/**
   	 * createImport is used to import data
   	 *
   	 * @param  Request $request request parameter
   	 * @return array   success or error data array
   	 * @author Spec Developer
   	 */
    public function createImport($request)
    {
   		$error = 0;
        $csv_path = $request->input('csv_path');
        $gallery_folder_path = $request->input('gallery_folder');
        $total_rows = $request->input('total_rows');
        $index    	= $request->input('index_key');

		$action		= $request->input('action');
        $isAbort 	= $request->input('is_abort');
        $productIds = $request->input('product_ids');

        $zipFolderName = '';
		$output = [];
		$is_exists = 0;

        $originalStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath);
        $thumbStoragePath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath);
        $oldProductId 	= $request->input('old_product_id');
   		if ($csv_path) {
           $importData_arr = $this->readCSVData($csv_path);
         	// Insert to MySQL database
		    $x = 0;
		    foreach ($importData_arr as $importData) {
		        if ($x == $index) {
		         	$productData = $this->getProductAndCategory($importData);
		            $proRs = $productData['product'] ?? [];
		            $productCatId = $productData['productCatId'] ?? null;
		            $input = array();
					if (!empty($proRs)) {
                        $productId = $proRs->id;
                        $is_exists = 1;
						$error = 1;
						if (empty($action)) {
							$errorMsg = str_replace("##SKU_ID##",trim($importData[3]),trans('admin-message.PRODUCT_SKU_ALREADY_EXISTS'));
							$output = array(
								'msg_type'      => 'error',
								'msg'           => $errorMsg,
								'row_number'    => $index,
								'product_id'   => $productId,
								'sku_id'        => trim($importData[3]),
								'is_exists' 	=> $is_exists,
							);
							break;
						}
                    } else {
						// Update existing record
						$product = $this->createOrUpdateProduct($importData, $productCatId, 'create');
	                	$productId = $product->id;
	                }
	                $this->importMedia($request, $importData, $productId, $x);
                    $output = array(
                                'msg_type'      => 'success',
                                'msg'           => 'Row number = '.$index.' added successfully!',
                                'row_number'    => $index,
                                'product_id'   	=> $productId,
                                'sku_id'        => trim($importData[3]),
								'is_exists' 	=> $is_exists,
								'total_rows'	=> $total_rows,
                            );
                   break;
		        }
		        $x++;
		    }
        }
        return $output;
    }

    /**
     * updateProductCount is used to get product count of when to import product
     *
     * @param  string $csv_path csv path
     * @return int    return counter if matched otherwise 0
     * @author Spec Developer
     */
    public function updateProductCount($csv_path)
    {
   		$updateCount = 0;
   		if (empty($csv_path)) {
   			return $updateCount;
   		}
   		$fileData = $this->readCSVData($csv_path);
   		if (!empty($fileData)) {
   			$skuArr = array_column($fileData, 3);
   			if (empty($skuArr)) {
	   			return $updateCount;
	   		}
	   		$updateCount = Products::whereIn('sku',$skuArr)->withTrashed()->count();
   		}
   		return $updateCount;
   }

   /**
    * importProductIndex is used to get index of product for import
    *
    * @param  string $csv_path csv path
    * @return array  index array of product if found otherwise empty array
    * @author Spec Developer
    */
   public function importProductIndex($csv_path)
   {
   		$indexArr = [];
   		if (empty($csv_path)) {
   			return $indexArr;
   		}
   		$importData_arr = $this->readCSVData($csv_path);
   		if (!empty($importData_arr)) {
   			$i = 0;
	   		foreach($importData_arr as $importData) {
	   		    $product = Products::select('id','sku')->where('sku',$importData[3])->withTrashed()->first();
	   		    if (empty($product)) {
	   				array_push($indexArr, $i);
	   			}
	   			$i++;
	   		}
   		}
   		return $indexArr;
    }

    /**
     * importCsvData is used to import product and media
     *
     * @param  Request $request request parameter
     * @return json    success or error json
     * @author Spec Developer
     */
    public function importCsvData(Request $request)
    {
    	try {
	        header('Content-type: text/html; charset=utf-8');
	        header("Cache-Control: no-cache, must-revalidate");
	        header ("Pragma: no-cache");
	        set_time_limit(0);
	        ob_implicit_flush(1);
	        $error = 0;
	        $csv_path = $request->input('csv_path');
	        $gallery_folder_path = $request->input('gallery_folder');
	        $total_rows = $request->input('total_rows');
	        $index    	= $request->input('index_key');
			$action		= $request->input('action');
	        $isAbort 	= $request->input('is_abort');
	        $productIds = $request->input('product_ids');

	        $zipFolderName = '';
			$output = $input = [];
			$is_exists = 0;
			$finalIndex = $index+1;
	        $oldProductId 	= $request->input('old_product_id');
	        $update = 0;
	        $override = 0;
	        $indexArr = [];
	        $updateCount = $this->updateProductCount($csv_path);
			$output['updateRowCnt'] = $updateCount;
			if (!empty($action)) {
				if ($action=='overwrite') {
					$output['oldProductId'] = $oldProductId;
					$output['importKey'] = $index;
					$output['productIds'] = $productIds;
				}
				if ($finalIndex == $updateCount) {
					$oldProductIdArr = explode(",", $request->input('OldProductIds'));
					$importIndexArr = explode(",", $request->input('importIndex'));
					$importProductIdArr = explode(",", $request->input('importProductId'));
					$createIndex = $this->importProductIndex($csv_path);
					$importIndexArr = array_merge($importIndexArr, $createIndex);
					if ($action=='overwrite') {
						array_push($oldProductIdArr, $oldProductId);
						array_push($importIndexArr, $index);
						array_push($importProductIdArr, $productIds);
					}
					$input['total_rows'] = $total_rows;
					$input['csv_path'] = $csv_path;
					$input['action'] = $action;
					$input['gallery_folder'] = $gallery_folder_path;
					$input['old_product_id'] = array_filter($oldProductIdArr, fn($value) => !is_null($value) && $value !== '');
					$input['index_key'] = array_filter($importIndexArr, fn($value) => !is_null($value) && $value !== '');
					$input['product_ids'] = array_filter($importProductIdArr, fn($value) => !is_null($value) && $value !== '');
					$override = 1;
				}

				if ($action == 'abort') {
					$imported = $this->abortImport($request);
				} else if (in_array($action, ['skip', 'overwrite'])) {
					if (!empty($override) && !empty($input['index_key'])) {
						$imported = $this->overrideImport($request, $input);
					} else {
						$imported = $this->skipImpoprt($request);
					}
				}
			} else {
				$imported = $this->createImport($request);
				$output['inserted_id'] = (!empty($imported['product_id']) && $imported['msg_type']=='success') ? $imported['product_id'] : null;
			}
		} catch (\Exception $e) {
			$imported = ['msg_type'=>'error', 'msg'=>$e->getMessage()];
		}
		echo json_encode(array_merge($imported, $output));die;
    }

   /**
    * uploadCSV Count csv rows after upload csv for import products.
    *
    * @param  Request $request Request parameter
    * @return json
    * @author Spec Developer
    */
    public function uploadCSV(Request $request)
    {
        $error = '';

		$zipFileLocation	= 	Storage::disk($this->fileSystemCloud)->path($this->productZipImportPath);
		$zipFilePath		=	'';
		$zipFolderPath		=	'';

        // ZIP file upload and extract code start here
        $zipFile = $request->file('zip_file');
        if ($zipFile) {
            $zipFileName 	= $zipFile->getClientOriginalName();
            $fileExtension 	= $zipFile->getClientOriginalExtension();
            $zipTempPath = $zipFile->getRealPath();
            $zipFileSize = $zipFile->getSize();
            $zipMimeType = $zipFile->getMimeType();

            $explodeFolder = explode(".".$fileExtension,$zipFileName);
            $zipFolderName = $explodeFolder[0];

            // Valid File Extensions
            $validExtension = array("zip");

            // 125MB in Bytes
            $maxFileSize = 131072000; // (in binary)
            if(in_array(strtolower($fileExtension),$validExtension)){
                if($zipFileSize <= $maxFileSize){
                    $zipFile->move($zipFileLocation,$zipFileName);
                    $file = fopen($zipFileLocation.$zipFileName,"r");
                    $zip = Zip::open($zipFileLocation.$zipFileName);
                    $zip->extract($zipFileLocation);
                    $zipFilePath = $zipFileLocation.$zipFileName;
                    $zipFolderPath = $zipFileLocation.$zipFolderName;

					if(is_dir($zipFolderPath)){
						$folders = scandir($zipFolderPath, 1);
						if(is_dir($zipFolderPath.'/'.$folders[0])){
							$error = "Found some other directory into zip folder.";
						}
					}

                }
            }
            else
            {
                // code pending for remove file which is not zip extension.
                $error = "Only zip file format is allowed.";
            }
        }

        // CSV file upload code start here
        $file = $request->file('csv_file');

		$csvLocation	= 	Storage::disk($this->fileSystemCloud)->path($this->productImportPath);
		$csvFilePath	= 	'';

        if ($file) {

            // File Details
            $csvFileName	= $file->getClientOriginalName();
            $extension		= $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;  // (in binary)

            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){

                if($fileSize <= $maxFileSize){

                    // Upload file
                    $file->move($csvLocation,$csvFileName);

                    // Import CSV to Database
					$csvFilePath = $csvLocation.$csvFileName;

                    $fp = fopen($csvFilePath, 'r');
                    // Headrow
                    $head = fgetcsv($fp, 4096, ';', '"');

                    $csvHeader = array('Product Name','Product Category','Description','SKU','Quantity','Cost Price ($)','Sell Price ($)','Gallery');
                    $headArr = explode(',', $head[0]);

                    $validCSV = 1;
                    foreach($headArr as $colName){
                        if(!in_array($colName,$csvHeader)){
                            $error = 'CSV file column not match!';
                            $validCSV = 0;
                            break;
                        }
                    }
                    $total_line = 1;
                    if($validCSV == 1){
                        $file_content	= file($csvFilePath, FILE_SKIP_EMPTY_LINES);
                        $total_line		= count($file_content);
                    }

                }
                else
                {
                    // code pending if file is greated than 2MB.
                    $error = 'CSV file should not be greater than 2MB.';
                }
            }
            else
            {
                // code pending for remove file which is not csv extension.
                $error = 'Only csv file format is allowed.';
            }
        }
        else
        {
            $error = 'Please select csv file';
        }

		$removeUploadedFiles = 0;
        if($error != '')
        {
            $output = array('error'  => $error);
			$removeUploadedFiles = 1;
        }
        else
        {
			if(($total_line - 1) > 50){
				// If csv row more than 50.
				$error = 'Maximum 50 products allowed for import.';
				$output = array('error'  => $error);
				$removeUploadedFiles = 1;
			}
            else
			{
				$output = array(
					'success'		=>	true,
					'total_line' 	=>	($total_line - 1),
					'csv_path'		=>	$csvFilePath,
					'zip_folder'	=>	$zipFolderPath,
				);
			}
        }

		if($removeUploadedFiles == 1){
			if (file_exists($csvFilePath)) {
				@unlink($csvFilePath);
			}

			if (file_exists($zipFolderPath)) {
				File::deleteDirectory($zipFolderPath);
			}

			if (file_exists($zipFilePath)) {
				$zip = Zip::open($zipFilePath);
				$zip->close();
				@unlink($zipFilePath);
			}
		}
        echo json_encode($output);
    }

	/*
        @Author : Spec Developer
        @Desc   : Total products.
        @Date   : 18/04/2022
    */
    public function totalProducts(){
        $totalProducts = Products::where('status', 0)->count();
        return $totalProducts;
    }

}