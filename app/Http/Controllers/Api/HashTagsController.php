<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HashTagsResource;
use App\Models\HashTags;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class HashTagsController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : Get All User profile details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 14/04/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/get-all-hashtag",
        * operationId="get-all-hashtag",
        * tags={"Get all-hashtag"},
        * summary="Get-all-hashtag",
        * description="Get all hashtag",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"search_keyword","page","perpage"},
        *               @OA\Property(property="search_keyword", type="text"),
        *               @OA\Property(property="page", type="text"),
        *               @OA\Property(property="perpage", type="text"),
            
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Get hash tag data successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Get hash tag data successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Whoops! Something went wrong. Please try again."),
        * )
        */
    public function getAllHashTag(Request $request)
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
            Log::info('Start get has tag data');
            
            /* start count has tag list*/
              $hasTagArr = HashTags::where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('hash_tag_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $hasTagCount = $hasTagArr->where(array('status' => 1))
            ->get();
            $numrows = count($hasTagCount);
            /* End count has tag list*/
            
            /* start get user list*/
            $usersIdArr = HashTags::where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('hash_tag_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $hasTagList = $usersIdArr->where(array('status' => 1))
                ->orderBy('hash_tag_name', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();
            /* End Get user list*/    

            $users = HashTagsResource::collection($hasTagList);

            $nextPage = LengthPager::makeLengthAware($users, $numrows, $perpage, []);

            if (!empty($users)) {
                Log::info('Get Has Tag list Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'hashTagList' => $users
                );
                return successResponse(trans('api-message.GET_HASTAG_DATA_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
