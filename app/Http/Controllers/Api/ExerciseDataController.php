<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExerciseDataResource;
use App\Models\ExerciseData;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ExerciseDataController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : get list of Exercise Data
        @Input  : page,parpage,serchtext
        @Output : \Illuminate\Http\Response
        @Date   : 24/03/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/search-exercise-list",
        * operationId="search-exercise-list",
        * tags={"Search exercise list"},
        * summary="search-exercise-list",
        * description="search-exercise-list",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"search_text","page","perpage"},
        *               @OA\Property(property="search_text", type="text"),
        *               @OA\Property(property="page", type="text"),
        *               @OA\Property(property="perpage", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="User has been added successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="User has been added successfully.",
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
    public function searchExerciseList(Request $request){
        try{
            //dd($request->search_text);
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
            //$user_id = $request->user_id;
            $numrows = 0;
            $exercises = "";
            if(isset($request->search_text) && !empty($request->search_text)){
                $search_keyword = $request->search_text;
                $exercise_count = ExerciseData::where('activity', 'like', '%' . $search_keyword . '%')
				->orWhere('specific_motion', 'like', '%' . $search_keyword . '%')
                ->orWhere('mets', 'like', '%' . $search_keyword . '%')
                ->get();
                $numrows = count($exercise_count);

                $exercise_list = ExerciseData::where('activity', 'like', '%' . $search_keyword . '%')
                ->orWhere('specific_motion', 'like', '%' . $search_keyword . '%')
                ->orWhere('mets', 'like', '%' . $search_keyword . '%')
                ->skip($offset)
                ->take($perpage)
                ->get();
                
                 $exercises = ExerciseDataResource::collection($exercise_list);
                
             }


            $nextPage = LengthPager::makeLengthAware($exercises, $numrows, $perpage, []);
            Log::info('get list of exercise data');

            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'exercise_data' => $exercises,
            );
            Log::info('create exercise list');
            return successResponse(trans('api-message.EXERCISE_LIST_FETCHED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch(\Exception $e){
            // Log Message
            Log::error('Get exercise list due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
