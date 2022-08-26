<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserExerciseDataResource;
use App\Models\UserExerciseData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class UserExerciseDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user_id = Auth::user()->id;
            $exerciseData = UserExerciseData::where(array('user_id' => $user_id))->get();
            $userExerciseData = UserExerciseDataResource::collection($exerciseData);
            $data = array(
                'exerciseData' => $userExerciseData,
            );
            
            return successResponse(trans('api-message.EXERCISE_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            // Log social login error messages
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('Create user exercise data');
        try {
                $exerciseData = new UserExerciseData();
                $exerciseData->user_id = Auth::user()->id;
                $exerciseData->exercise_id = !empty($request->exerciseId) ? $request->exerciseId : NULL;
                $exerciseData->calory = !empty($request->calory) ? $request->calory : '';
                $exerciseData->time_spend = !empty($request->time_spend) ? $request->time_spend : NULL;
                $exerciseData->description = !empty($request->description) ? $request->description : NULL;
                $exerciseData->notes = !empty($request->notes) ? $request->notes : '';
                $exerciseData->met = !empty($request->met) ? $request->met : '';
                $exerciseData->date_and_time = $request->DateAndTime;
                $exerciseData->save();
            Log::info('Add exercise detail successfully');
            $messge = trans('api-message.ADD_EXERCISE_DETIAL_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Create exercise details :: message :'.$message);
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
        * @OA\Post(
        * path="/api/v1/edit-exercise-data",
        * operationId="Edit Exercise Data",
        * tags={"Edit Exercise Data"},
        * summary="Edit Exercise Data",
        * description="Edit Exercise Data",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"id","exerciseId","calory","time_spend","description","met","notes"},
        *               @OA\Property(property="id", type="text"),
        *               @OA\Property(property="exerciseId", type="text"),
        *               @OA\Property(property="calory", type="text"),
        *               @OA\Property(property="time_spend", type="text"),
        *               @OA\Property(property="description", type="text"),
        *               @OA\Property(property="met", type="text"),
        *               @OA\Property(property="notes", type="text"),
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
    public function update(Request $request)
    {
        try {
                $id = $request->id;
                $exerciseData['exercise_id'] = !empty($request->exerciseId) ? $request->exerciseId : NULL;
                $exerciseData['calory'] = !empty($request->calory) ? $request->calory : '';
                $exerciseData['time_spend'] = !empty($request->time_spend) ? $request->time_spend : NULL;
                $exerciseData['description'] = !empty($request->description) ? $request->description : '';
                $exerciseData['met'] = !empty($request->met) ? $request->met : 0;
                $exerciseData['notes'] = !empty($request->notes) ? $request->notes : '';
                $exerciseData['date_and_time'] = $request->DateAndTime;
                UserExerciseData::where('id', $id)->update($exerciseData);

                $result = UserExerciseData::where(array('id' => $id))->first();
                $userExerciseData = new UserExerciseDataResource($result);

                $data = array(
                    'exerciseData' => $userExerciseData,
                );
                Log::info('Edit exercise detail successfully');
                return successResponse(trans('api-message.EXERCISE_DETAILS_UPDATED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Edit exercise details :: message :'.$message);
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Remove the specified resource from storage.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 15/03/2022
    */
    public function destroy($id)
    {
        try {
            Log::info('Delete User exercise data API started !!');
            $userExerciseData = UserExerciseData::find($id);
            if (empty($userExerciseData)) {
                Log::warning('Exercise detail not found with id:' . $id);
                return errorResponse(trans('api-message.EXERCISE_DETAILS_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);    
            }
            $userExerciseData->delete();

            Log::info('Delete exercise detail with id:' . $id);
            return successResponseWithoutData(trans('api-message.DELETE_EXERCISE_DETIAL_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Unable to delete Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_DELETE_EXERCISE_DETAILS'),STATUS_CODE_ERROR);
        }
    }


    /*
        @Author : Ritesh Rana
        @Desc   : Get Exercise Data specified resource from storage.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */
    public function getExerciseData(Request $request)
    {
        try {
            $exerciseDetail = UserExerciseData::where('id',$request->id)->first();
            
            if (empty($exerciseDetail)) {
                Log::warning('Exercise not found with id:' . $request->gtin_upc);
                return errorResponse(trans('api-message.EXERCISE_NOT_FOUND'), STATUS_CODE_INSUFFICIENT_DATA);
            }
            
            $userExerciseData = new UserExerciseDataResource($exerciseDetail);
            $data = array(
                'exerciseData' => $userExerciseData,
            );

            Log::info('Get exercise detail successfully');
            return successResponse(trans('api-message.GET_EXERCISE_DETAILS_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Edit exercise details :: message :'.$message);
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
