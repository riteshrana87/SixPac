<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Products;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : Get All Categorys details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 09/05/2022
    */
    public function getAllProducts(Request $request)
    {
        
        try {
            $search_keyword = $request->search_keyword;
            $categoryData = [];
            if(isset($request->categoryId) && !empty($request->categoryId)){
                $categoryId = $request->categoryId;
                $categoryData = array_unique(explode(",",$categoryId));
            }
            
            
            If (!empty($request->page)) {
                $page = $request->page;
            } else {
                $page = "1";
            }
            If (!empty($request->perpage)) {
                $perpage = $request->perpage;
            } else {
                $perpage = Config::get('constant.LIST_PER_PAGE');
            }
            $offset = ($page - 1) * $perpage;
            Log::info('Start Get Category Data');
            
            /* start count product list*/
            $productsData = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }))->withCount('productGalleries')
            ->where(function ($query) use($search_keyword,$categoryData) {
				if($search_keyword){ 
					$query->where('product_title', 'like', '%' . $search_keyword . '%');
				}

                if(!empty($categoryData)){
                    $query->whereIn('category_id',$categoryData);
                }
            });
            $productCount= $productsData->orderby('id','DESC')->get();
            $numrows = count($productCount);
            /* End count products list*/
            
            /* start get products list*/
            $productsList = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }))->withCount('productGalleries')
            ->where(function ($query) use($search_keyword,$categoryData) {
				if($search_keyword){ 
					$query->where('product_title', 'like', '%' . $search_keyword . '%');
				}

                if(!empty($categoryData)){
                    $query->whereIn('category_id',$categoryData);
                }
            });
            $productArr = $productsList->orderby('id','DESC')
            ->skip($offset)
            ->take($perpage)
            ->get();
            
            /* End Get product list*/    
            $productListData = ProductResource::collection($productArr);
            $nextPage = LengthPager::makeLengthAware($productListData, $numrows, $perpage, []);

            if (!empty($productListData) && count($productListData) > 0) {
                Log::info('Product list Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'productData' => $productListData
                );
                return successResponse(trans('api-message.GET_PRODUCT_LIST'), STATUS_CODE_SUCCESS, $data);
            } else {
                return errorResponse(trans('api-message.RECORD_NOT_FOUND'),STATUS_CODE_NOT_FOUND);
            }

        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    /*
        @Author : Ritesh Rana
        @Desc   : Display the specified resource.
        @Input  : int $id
        @Output : \Illuminate\Http\Response
        @Date   : 09/05/2022
    */
    public function productDetail($id)
    {
        try {
            $productsDetail = Products::with(array('productCategory'=> function($query) {
                $query->select('id','category_name');
            }))
            ->withCount('productGalleries')
            ->where('id',$id)->first();
            
            if (!empty($productsDetail)) {
                $productsData = new ProductResource($productsDetail);
                Log::info('Product list Details');
                $data = array(
                    'productData' => $productsData,
                );
                return successResponse(trans('api-message.GET_PRODUCT_DETAIL'), STATUS_CODE_SUCCESS, $data);
            } else {
                return errorResponse(trans('api-message.RECORD_NOT_FOUND'),STATUS_CODE_NOT_FOUND);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to get product detail due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
