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

class ProductsController extends Controller
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
		@Desc   : Fetch products listing.
		@Output : \Illuminate\Http\Response
		@Date   : 21/03/2022
	*/

    public function index(Request $request){
    	$data['page_title'] = 'Products';
		$data['page_js'] = array(
            'backend/assets/business/js/products.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css',
            'plugins/table/datatable/datatables.css',
            'plugins/fancybox/dist/jquery.fancybox.min.css',
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
            'plugins/table/datatable/datatables.js',
            'plugins/fancybox/dist/jquery.fancybox.min.js',
        );

		$data['init'] = array(
			'Products.init();'
		);


        if ($request->ajax()) {
        	$draw = $request->get('draw');
            $start = $request->get("start");
            $rowPerPage = $request->get("length"); 
            $searchValue = $request->get('search')['value'] ?? '';
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $sortOrder = $order_arr[0]['dir'];

        	$totalRecords = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }, 'usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))->select('count(*) as allcount')->count();

        	$productObj = Products::with(array('productCategory'=> function ($query) {
                $query->select('id','category_name');
            }, 'usersData'=> function ($query) {
                $query->select('id','user_name','role');
            }))->when(!empty($searchValue), function ($query) use ($searchValue) {
				$query->whereHas('productCategory', function ($query2) use ($searchValue) {
					$query2->where('category_name', 'like', '%' .$searchValue . '%');
				})->OrWhereHas('usersData.business', function ($query3) use ($searchValue) {
					$query3->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
				})->OrWhereHas('usersData', function ($query4) use ($searchValue) {
					$query4->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
				})->OrWhere('product_title', 'like', '%'.$searchValue.'%');
            });

            $totalFilteredRows = $productObj->count();

            $productsData = $productObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();
            $data_arr = [];
            $data_arr = $productsData;
            foreach($productsData as $key => $row) {
            	$data_arr[$key]->category_id = ($row->productCategory->category_name) ?? '-';
            	if ($row->usersData) {
            		$data_arr[$key]->user_id = (($row->usersData->role==3) ? $row->usersData->business->company_name : $row->usersData->user_name) ?? '-';
            	}            	
            	$data_arr[$key]->status = ($row->status == 0) ? '<label class="label label-danger">Deactive</label>' : '<label class="label label-success">Active</label>';
            	$btn = '';
				$btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/products/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
				$btn .= '<a class="editRecord ml-2 mr-2" href="'.url('business/products/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
				$btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/products/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
				$data_arr[$key]->action = $btn;
            }

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
             );
           echo json_encode($response); exit;
        }
        return view('business.products.list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add products.
		@Output : \Illuminate\Http\Response
		@Date   : 21/03/2022
	*/

	public function add(){

		$data['page_title'] = 'Add Product';
        $data['page_js']    = array(
            'backend/assets/business/js/add_products.js'
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

		$data['init'] = array('Products.add();');
		$data['product_category'] = ProductCategory::where('status',1)->orderby('category_name','ASC')->get();
        return view('business.products.add',$data);
	}

	/*
		@Author : Spec Developer
		@Desc   : Store new product data.
		@Output : \Illuminate\Http\Response
		@Date   : 21/03/2022
	*/
    public function store(Request $request){

		try {
            $fileValidationArr = array();

            $textValidationArr = array(
                'product_title'			=>	'required|unique:products,product_title',
				'category_name'			=>	'required',
				'product_description'	=>	'required',
				'sku'					=>	'required',
				'quantity'				=>	'required',
				'cost_price'			=>	'required',
				'sell_price'			=>	'required',
            );
            $validationArr = array_merge($textValidationArr,$fileValidationArr);

            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
				Log::info('Add product by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

            $obj = new Products([
                'product_title'			=> trim($request->get('product_title')),
                'category_id' 			=> $request->get('category_name'),
                'product_description' 	=> trim($request->get('product_description')),
                'sku' 					=> trim($request->get('sku')),
                'quantity' 				=> trim($request->get('quantity')),
                'cost_price'			=> trim($request->get('cost_price')),
                'sell_price'			=> trim($request->get('sell_price')),
                'user_id'        		=> Auth::user()->id,
                'status'				=> $request->get('status'),
            ]);
            $obj->save();
            $productId = $obj->id;

            $video_thumb     = $request->get('video_thumb');

            $thumbArr = [];
            if(count((array) $request->mediaThumb) > 0){
                foreach ($request->mediaThumb as $key => $thumb) {
                    $base64_str = substr($thumb, strpos($thumb, ",")+1);
                    $file = base64_decode($base64_str);
                    $thumbArr[$key] = $file;
                }
            }

			if(count((array)$request->get('media')) > 0){
				$i = 1;
				foreach (request()->media as $key => $gallery) {
					$base64_str = substr($gallery, strpos($gallery, ",")+1);
					$file = base64_decode($base64_str);
					$extension = strtolower(explode('/', explode(':', substr($gallery, 0, strpos($gallery, ';')))[1])[1]);
					// $fileName = Str::random(10).'.'.$extension;
                    // $fileName = $key.'_'.$productId.'.'.$extension;
                    // $thumbFileName = $key.'_'.$productId.'_thumb.'.$extension;

                    $mediaExtArr = Config::get('constant.MEDIA_EXTENSION');
                    if(!empty($mediaExtArr[$extension])){
                        $extension = $mediaExtArr[$extension];
                    }
                    $fileName = $i.'_'.$productId.'.'.$extension;
                    $thumbFileName = $i.'_'.$productId.'_thumb.'.$extension;

					Storage::disk('public')->put($this->productOriginalImagePath.$fileName, $file);

					if (!empty($thumbArr) && array_key_exists($key, $thumbArr)) {
                        Storage::disk($this->fileSystemCloud)->put($this->productThumbImagePath.$thumbFileName, $thumbArr[$key]);
                    } else {
	                    if(in_array($extension, Config::get('constant.IMAGE_EXTENSION'))){
	                        $originalPath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath.$fileName);
	                        $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath.$thumbFileName);

	                        $img = Image::make($originalPath); // Open an image file
	                        $img->resize($this->productThumbImageWidth, $this->productThumbImageHeight); // Resize image
	                        $img->save($thumbPath); // Save file into destination folder
						}
					}
					$fileLength = 0;

                    if(in_array($extension, Config::get('constant.VIDEO_EXTENSION'))){
						$getID3 = new getID3;
						$videoPath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath.$fileName);
						$video_file = $getID3->analyze($videoPath);
						// $duration_string = $video_file['playtime_string'];	// Get the duration in string
						$fileLength = $video_file['playtime_seconds']; // Get the duration in seconds

                        if(!empty($video_thumb)){

                            $thumbUrl    =   $video_thumb[$key];

                            $base64_str = substr($thumbUrl, strpos($thumbUrl, ",")+1);
                            $file       = base64_decode($base64_str);
                            $thumbExtension  = strtolower(explode('/', explode(':', substr($thumbUrl, 0, strpos($thumbUrl, ';')))[1])[1]);
                            // $fileName = Str::random(10).'.'.$extension;
                            $thumbFileName = $key.'_'.$productId.'_thumb.'.$thumbExtension;

                            Storage::disk('public')->put($this->productThumbImagePath.$thumbFileName, $file);

                            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath.$thumbFileName);
                            $img = Image::make($thumbPath); // Open an image file
                            $img->resize($this->productThumbImageWidth, $this->productThumbImageHeight); // Resize image
                            $img->save($thumbPath);
                        }
					}

					$file_original_url = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath.$fileName);
					$fileSize = filesize($file_original_url);

					$fileObj = new ProductGallery([
						'product_id'    =>  $productId,
						'file_name'     =>  $fileName,
                        'thumb_name'    =>  $thumbFileName,
                        'media_order'	=> 	$i,
						'file_type'     =>  $extension,
						'file_length'   =>  $fileLength,
						'file_size'     =>  $fileSize,
						'is_transacoded'=>  'transacoded',
						'status'        =>  1,
					]);
					$fileObj->save();
					$i++;
				}
			}

            Alert::success('Success', 'Product has been added!.', 'success');
			return redirect('business/products');
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/products/add');
        }
    }

    /*
		@Author : Spec Developer
		@Desc   : Edit product details form.
		@Output : \Illuminate\Http\Response
		@Date   : 21/03/2022
	*/

    public function editProduct($id){

        $data['page_title'] = 'Edit Product';
        $data['page_js']    = array(
            'backend/assets/business/js/edit_products.js'
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

		$data['init'] = array();
        $data['data'] =   Products::where('id',$id)->first();
		$data['product_category'] = ProductCategory::where('status',1)->orderby('category_name','ASC')->get();

		$galleryRs = ProductGallery::select('id','media_order','file_name','thumb_name','file_type','file_length','file_size')->where('product_id',$id)->orderby('media_order')->get();
		$data['prodGallery'] = array();

        $x = 0;
		if(count((array)$galleryRs) > 0){
			foreach($galleryRs as $gallery){
				$data['prodGallery']['file_url'][$x] = !empty($gallery->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->productOriginalImagePath.$gallery->file_name) : asset('backend/assets/images/no-post.png');
				$data['prodGallery']['gallery_id'][$x] = $gallery->id;
				$data['prodGallery']['file_name'][$x] = $gallery->file_name;
                $data['prodGallery']['thumb_url'][$x]    = !empty($gallery->thumb_name) ? Storage::disk($this->fileSystemCloud)->url($this->productThumbImagePath.$gallery->thumb_name) : asset('backend/assets/images/no-post.png');
				$data['prodGallery']['file_type'][$x] = $gallery->file_type;
				$data['prodGallery']['file_length'][$x] = $gallery->file_length;
				$data['prodGallery']['file_size'][$x] = $gallery->file_size;
				$data['prodGallery']['media_order'][$x] = $gallery->media_order;
				if (in_array($gallery->file_type, Config::get('constant.IMAGE_EXTENSION'))) {
					$data['prodGallery']['original_image'][$x]    = !empty($gallery->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->productOriginalImagePath.$gallery->file_name) : asset('backend/assets/images/no-post.png');
				}
				$x++;
			}
		}
		$data['totalMedia'] = $x;


        return view('business.products.edit',$data);
    }

    /**
     * getProductGallery is used to get product gallery id
     *
     * @param  int $productId product id
     * @param  int $fileCount number of files count
     * @return mix            file index if found othrwise false
     * @author Spec Developer
     */
    public function getProductGallery($productId, $fileCount)
    {
    	$galleries = ProductGallery::where('product_id', $productId)->pluck('file_name')->toArray();
    	if (!empty($galleries)) {
    		$oldGalleryIds = [];
    		foreach($galleries as $gallery){
    			$media = explode('_',$gallery);
    			array_push($oldGalleryIds, $media[0]);
    		}
    		$totalCount = count($galleries) + $fileCount;
    		for($i=1; $i<=$totalCount; $i++){
    			if (!in_array($i, $oldGalleryIds)) {
    				return $i;
    			}
    		}
    	}
    	return false;
    }

	/*
		@Author : Spec Developer
		@Desc   : Update product details.
		@Output : \Illuminate\Http\Response
		@Date   : 21/03/2022
	*/

	public function updateProduct(Request $request){

        try {
        	// dd($request->all());
            $id	= $request->input('product_id');
			$validator = Validator::make($request->all(), [
				'product_title'			=>	'required|unique:products,product_title,'.$id,
				'category_name'			=>	'required',
				'product_description'	=>	'required',
				'sku'					=>	'required',
				'quantity'				=>	'required',
				'cost_price'			=>	'required',
				'sell_price'			=>	'required',
			]);

			if ($validator->fails()) {
				Log::info('Edit product by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

			$oldMediaArr	= $request->input('old_media');
			$galleryArr = ProductGallery::select('id')->where('product_id',$id)->get();
			$oldMediaIds = array();
			$oldMediaOrders = $request->input('oldMediaOrder');
			if(count($galleryArr) > 0){
				foreach($galleryArr as $mediaArr){
					$oldMediaIds[] = $mediaArr->id;
				}
			}

            $originalPath	= Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath);
            $thumbPath		= Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath);

			if(isset($oldMediaArr) && !empty($oldMediaArr)){
				$delGallleryArr = array_diff($oldMediaIds, $oldMediaArr);
				if(count((array)$delGallleryArr) > 0){
                    foreach($delGallleryArr as $mediaId){
						deleteProductMediaFromMediaId($mediaId, $originalPath, $thumbPath, 0);
					}
				}
			}

            if(count((array)$oldMediaArr) == 0){
                deleteProductAndMedia($id, $originalPath, $thumbPath, 0);
            }

			$input['product_title'] = trim($request->product_title);
            $input['category_id'] 	= $request->category_name;
            $input['product_description'] = trim($request->product_description);
            $input['sku'] 			= trim($request->sku);
            $input['quantity'] 		= trim($request->quantity);
            $input['cost_price']	= trim($request->cost_price);
            $input['sell_price']	= trim($request->sell_price);
            $input['status']		= $request->status;

            Products::where('id', $id)->update($input);
            if (!empty($oldMediaOrders)) {
	            foreach ($oldMediaOrders as $galleryId => $mediaOrder) {
	            	ProductGallery::where('id', $galleryId)->update(['media_order' => (int)$mediaOrder]);
           		}
            }

           if (count((array) $request->oldThumb) > 0) {
                foreach ($request->oldThumb as $galleryId => $thumb) {
                    $base64_str = substr($thumb, strpos($thumb, ",")+1);
                    $file = base64_decode($base64_str);                    
                    $gallery = ProductGallery::where('id', $galleryId)->first();
                    if ($gallery) {
                        $filePath = $this->productThumbImagePath.$gallery->thumb_name;
                        if (Storage::disk($this->fileSystemCloud)->exists($filePath)) {
                            Storage::disk($this->fileSystemCloud)->delete($filePath);
                            Storage::disk($this->fileSystemCloud)->put($filePath, $file);
                        }
                    }
                }
            }

            $thumbArr = [];
            if (count((array) $request->newThumb) > 0) {
                foreach ($request->newThumb as $key => $thumb) {
                    $base64_str = substr($thumb, strpos($thumb, ",")+1);
                    $file = base64_decode($base64_str);
                    $thumbArr[$key] = $file;
                }
            }

			/** Upload images/video code start here **/
            $video_thumb    = $request->input('video_thumb');
			if(count((array)$request->get('media')) > 0){
				$i = 1;
				$index = 0;
				foreach (request()->media as $key => $gallery) {
					$base64_str = substr($gallery, strpos($gallery, ",")+1);
					$file = base64_decode($base64_str);
					$extension = strtolower(explode('/', explode(':', substr($gallery, 0, strpos($gallery, ';')))[1])[1]);
					// $fileName = Str::random(10).'.'.$extension;
                     $mediaExtArr = Config::get('constant.MEDIA_EXTENSION');
                    if(!empty($mediaExtArr[$extension])){
                        $extension = $mediaExtArr[$extension];
                    }
                    $fileCount = count((array)$request->get('media'));
            		$fileIndex = $this->getProductGallery($id, $fileCount);
            		// dd($fileIndex);
                    $fileName = $fileIndex.'_'.$id.'.'.$extension;
                    $thumbFileName = $fileIndex.'_'.$id.'_thumb.'.$extension;

					Storage::disk('public')->put($this->productOriginalImagePath.$fileName, $file);

					if (!empty($thumbArr) && array_key_exists($key, $thumbArr)) {
                        Storage::disk($this->fileSystemCloud)->put($this->productThumbImagePath.$thumbFileName, $thumbArr[$key]);
                    } else {
	                    if(in_array($extension, Config::get('constant.IMAGE_EXTENSION'))){
	                        $originalPath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath.$fileName);
	                        $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath.$thumbFileName);

	                        $img = Image::make($originalPath); // Open an image file
	                        $img->resize($this->productThumbImageWidth, $this->productThumbImageHeight); // Resize image
	                        $img->save($thumbPath); // Save file into destination folder
						}
					}
					$fileLength = 0;
                    // if(in_array($extension, Config::get('constant.VIDEO_EXTENSION'))){
					if(in_array($extension, Config::get('constant.VIDEO_EXTENSION'))){
						$getID3 = new getID3;
						$videoPath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath.$fileName);
						$video_file = $getID3->analyze($videoPath);
						$duration_string = $video_file['playtime_string'];	// Get the duration in string
						$fileLength = $video_file['playtime_seconds']; // Get the duration in seconds

                        if(!empty($video_thumb)){

                            $thumbUrl    =   $video_thumb[$key];

                            $base64_str = substr($thumbUrl, strpos($thumbUrl, ",")+1);
                            $file       = base64_decode($base64_str);
                            $thumbExtension  = strtolower(explode('/', explode(':', substr($thumbUrl, 0, strpos($thumbUrl, ';')))[1])[1]);
                            // $fileName = Str::random(10).'.'.$extension;
                            $thumbFileName = $key.'_'.$id.'_thumb.'.$thumbExtension;

                            Storage::disk('public')->put($this->productThumbImagePath.$thumbFileName, $file);

                            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath.$thumbFileName);
                            $img = Image::make($thumbPath); // Open an image file
                            $img->resize($this->productThumbImageWidth, $this->productThumbImageHeight); // Resize image
                            $img->save($thumbPath);
                        }
					}

					$file_original_url = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath.$fileName);
					$fileSize = filesize($file_original_url);
					$fileObj = new ProductGallery([
						'product_id'    =>  $id,
						'file_name'     =>  $fileName,
                        'thumb_name'    =>  $thumbFileName,
                        'media_order'	=> 	$key,
						'file_type'     =>  $extension,
						'file_length'   =>  $fileLength,
						'file_size'     =>  $fileSize,
						'is_transacoded'=>  'transacoded',
						'status'        =>  1,
					]);
					$fileObj->save();
					$i++;
					$index++;
				}
			}


			/** Upload images/video code end here **/

            Alert::success('Success', 'Product details updated.', 'success');
		    return redirect('business/products');

        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage().' at line '.$e->getLine(), 'error');
		    return redirect('business/products');
        }
	}

	/*
        @Author : Spec Developer
        @Desc   : Check product is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 21/03/2022
    */

    public function checkProductExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = Products::where('product_title', $request->product_title)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = Products::where('product_title', $request->product_title)->count();
        }

        if($result == 0){
            $return =  true;
        }
        else{
            $return= false;
        }
        echo json_encode($return);
        exit;
    }

	/*
        @Author : Spec Developer
        @Desc   : Check product SKU is already exists or not.
        @Output : \Illuminate\Http\Response
        @Date   : 21/03/2022
    */

    public function checkProductSkuExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = Products::where('sku', $request->sku)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = Products::where('sku', $request->sku)->count();
        }

        if($result == 0){
            $return =  true;
        }
        else{
            $return= false;
        }
        echo json_encode($return);
        exit;
    }

	/*
        @Author : Spec Developer
        @Desc   : View product details.
        @Date   : 21/03/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;

		$row['data'] = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }, 'usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))
            ->select('id','product_title','product_description','category_id','sku','quantity','cost_price','sell_price','created_at','user_id','status')
            ->orderby('id','DESC')
			->find($id);

        $row['media'] = ProductGallery::select('id','file_name','file_type','thumb_name')->where('product_id',$id)->orderby('media_order')->get();

        if(count((array)$row['media']) > 0){
            $x = 0;
			foreach($row['media'] as $results){
				$file = !empty($results->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->productOriginalImagePath.$results->file_name) : asset('backend/assets/images/no-post.png');
					$row['media'][$x]['file_url'] = $file;
                    $row['media'][$x]['file_type'] = $results->file_type;
                    $row['media'][$x]['thumb_url'] = !empty($results->thumb_name) ? Storage::disk($this->fileSystemCloud)->url($this->productThumbImagePath.$results->thumb_name) : asset('backend/assets/images/no-post.png');
                $x++;
			}
		}
		return view('business.products.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete product record.
        @Output : \Illuminate\Http\Response
        @Date   : 01/04/2022
    */

    public function destroy($id){
		$obj = Products::find($id);
		$obj->delete();
        Alert::success('Success', 'Product has been deleted successfully!', 'success');
		return redirect('business/products');
    }

	/*
        @Author : Spec Developer
        @Desc   : Archive product details.
        @Date   : 28/03/2022
    */

    public function archiveProducts(Request $request){
        $data['page_title'] = 'Archive Products';
		$data['page_js'] = array(
            'backend/assets/business/js/products.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css'
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'Products.archiveProducts();'
        );

		if ($request->ajax()) {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowPerPage = $request->get("length"); 
            $searchValue = $request->get('search')['value'] ?? '';
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $sortOrder = $order_arr[0]['dir'];
            $totalRecords = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }, 'usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))->onlyTrashed()->select('count(*) as allcount')->count();

            $productObj = Products::with(array('productCategory'=> function ($query) {
                $query->select('id','category_name');
            }, 'usersData'=> function ($query) {
                $query->select('id','user_name','role');
            }))->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->whereHas('productCategory', function ($query2) use ($searchValue) {
                        $query2->where('category_name', 'like', '%' .$searchValue . '%');
                    })->OrWhere(function($query3) use ($searchValue) {
                        $query3->whereHas('usersData.business', function ($query4) use ($searchValue) {
                            $query4->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                        })->OrWhereHas('usersData', function ($query5) use ($searchValue) {
                            $query5->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                        });
                    })->OrWhere('product_title', 'like', '%'.$searchValue.'%');
                });
            })->onlyTrashed();	

            $totalFilteredRows = $productObj->count();

            $productsData = $productObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();

            $data_arr = [];
            $data_arr = $productsData;
            foreach($productsData as $key => $row) {
                $data_arr[$key]->category_id = ($row->productCategory->category_name) ?? '-';
                if ($row->usersData) {
                    $data_arr[$key]->user_id = (($row->usersData->role==3) ? $row->usersData->business->company_name : $row->usersData->user_name) ?? '-';
                }               
                $data_arr[$key]->status = ($row->status == 0) ? '<label class="label label-danger">Deactive</label>' : '<label class="label label-success">Active</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/products/archive-view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="restore ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" title="Restore" data-url="'.url('business/products/restore').'" data-target="#restoreModal" ><i class="fa fa-refresh fa-action-restore"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/products/force-delete').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
                $data_arr[$key]->action = $btn;
            }

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
             );
           echo json_encode($response); exit;
        }

        return view('business.products.archive_list',$data);
    }

	/*
        @Author : Spec Developer
        @Desc   : View archive products record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */

    public function viewArchive(Request $request){

		$id     =	$request->object_id;

		$row['data'] = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }, 'usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))
            ->select('id','product_title','product_description','category_id','sku','quantity','cost_price','sell_price','created_at','user_id','status')
            ->orderby('id','DESC')
			->onlyTrashed()
			->find($id);

        $row['media'] = ProductGallery::select('id','file_name','file_type')->where('product_id',$id)->orderby('id','desc')->get();

        if(count((array)$row['media']) > 0){
            $x = 0;
			foreach($row['media'] as $results){
				$file = !empty($results->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->productOriginalImagePath.$results->file_name) : asset('backend/assets/images/no-post.png');
					$row['media'][$x]['file_url'] = $file;
                    $row['media'][$x]['file_type'] = $results->file_type;
                    $row['media'][$x]['thumb_url'] = !empty($results->thumb_name) ? Storage::disk($this->fileSystemCloud)->url($this->productThumbImagePath.$results->thumb_name) : asset('backend/assets/images/no-post.png');
                $x++;
			}
		}

		return view('business.products.archive_view',$row);
    }

	/*
        @Author : Spec Developer
        @Desc   : Force delete product record.
        @Output : \Illuminate\Http\Response
        @Date   : 21/04/2022
    */

    public function forceDelete($id){
        // ProductGallery::where('product_id', $id)->delete();
        // Products::where('id', $id)->forceDelete();
        $galleryRs = ProductGallery::select('id','file_name','thumb_name')->where('product_id',$id)->get();

		if(count((array)$galleryRs) > 0){
            $originalPath = Storage::disk($this->fileSystemCloud)->path($this->productOriginalImagePath);
            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->productThumbImagePath);

			foreach($galleryRs as $galleryObj){
                deleteProductMediaFromMediaId($galleryObj->id, $originalPath, $thumbPath, 0);
			}
		}
		Products::where('id', $id)->forceDelete();
        Alert::success('Success', 'Product has been deleted permanently!', 'success');
        return redirect('business/products/archive-products');
    }

	/*
        @Author : Spec Developer
        @Desc   : Restore product record.
        @Output : \Illuminate\Http\Response
        @Date   : 21/04/2022
    */

    public function restoreProduct($id){

        ProductGallery::where('product_id', $id)->withTrashed()->restore();

        //$input['status'] = 1;
        //Products::where('id', $id)->update($input);

		Products::withTrashed()->find($id)->restore();

        Alert::success('Success', 'Product has been restore successfully!', 'success');
		return redirect('business/products/archive-products');
    }

}