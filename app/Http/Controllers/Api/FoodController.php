<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodsMasterResource;
use App\Models\Foods;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FoodController extends Controller
{
    /*
        @Author : Spec India
        @Desc   : Get food details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/food-data",
        * operationId="food-data",
        * tags={"Food data"},
        * summary="food-data",
        * description="food-data",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"keyword","page","limit"},
        *               @OA\Property(property="keyword", type="text"),
        *               @OA\Property(property="page", type="text"),
        *               @OA\Property(property="limit", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Get food data successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Get food data successfully.",
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
    public function getFoodData(Request $request){
        try{
            $keyword	= $request->keyword;
            
            $validator = Validator::make($request->all(), [
                'keyword'		=> 'required|min:3',
                'page'			=> 'required|numeric',
                'limit'			=> 'required|numeric',
            ]);
            if ($validator->fails()) {
                $message = trans('api-message.PARAMETER_OR_PARRAMETER_VALUE_NOT_MATCH');
                return errorResponse($message,STATUS_CODE_ERROR);
                exit;
            }

            If (!empty($request->page)) {
                $page = $request->page;
            } else {
                $page = "1";
            }
            If (!empty($request->limit)) {
                $perpage = $request->limit;
            } else {
                $perpage = Config::get('constant.LIST_PER_PAGE');
            }
            $offset = ($page - 1) * $perpage;

            $foodData_count = Foods::where('description', 'LIKE', '%' . $keyword . '%')
            ->orderByRaw('CHAR_LENGTH(description)')
            ->orderBy('description', 'ASC')
            ->groupBy('description','brand_name')
            ->get();
            $numrows = count($foodData_count);

            $foodData = Foods::where('description', 'LIKE', '%' . $keyword . '%')
                ->orderByRaw('CHAR_LENGTH(description)')
                ->orderBy('description', 'ASC')
                ->groupBy('description','brand_name')
                ->skip($offset)
                ->take($perpage)
                ->get();

            $foodList = FoodsMasterResource::collection($foodData);

            $nextPage = LengthPager::makeLengthAware($foodList, $numrows, $perpage, []);
            

            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'foodData' => $foodList
            );
            
            return successResponse(trans('api-message.FOOD_DETAILS_LISTING'), STATUS_CODE_SUCCESS, $data);
        
        } catch(\Exception $e){
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
