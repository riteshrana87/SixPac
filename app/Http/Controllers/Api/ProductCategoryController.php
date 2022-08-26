<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ProductCategoryController extends Controller
{
   
    /*
        @Author : Ritesh Rana
        @Desc   : Get All Categorys details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 09/05/2022
    */
    public function getAllCategory(Request $request)
    {
        try {
            $search_keyword = $request->search_keyword;

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
            
            /* start count user list*/
              $categoryArr = ProductCategory::select('id','category_name','created_at','created_by','status')->where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('category_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $categoryCount = $categoryArr->where(array('status' => 1))->get();
            $numrows = count($categoryCount);
            /* End count user list*/
            
            /* start get user list*/
            $categoryIdArr = ProductCategory::select('id','category_name','created_at','created_by','status')->where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('category_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $category = $categoryIdArr->where(array('status' => 1))
                ->orderBy('id', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();

            /* End Get user list*/    

            $productCategory = ProductCategoryResource::collection($category);

            $nextPage = LengthPager::makeLengthAware($productCategory, $numrows, $perpage, []);

            if (!empty($productCategory)) {
                Log::info('Users Profile Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'productCategory' => $productCategory
                );
                return successResponse(trans('api-message.GET_PRODUCT_CATEGORY_DATA_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
}
