<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FoodsMasterResource;
use App\Http\Resources\UserExerciseDataResource;
use App\Http\Resources\UserFoodDataResource;
use App\Models\Foods;
use App\Models\UserExerciseData;
use App\Models\UserFoodData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use phpseclib3\Crypt\PublicKeyLoader;

class UserFoodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
        * @OA\Post(
        * path="/api/v1/user-food-data",
        * operationId="user-food-data",
        * tags={"user food data"},
        * summary="user food data",
        * description="user food data",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"endDate","startDate"},
        *               @OA\Property(property="startDate", type="text"),
        *               @OA\Property(property="endDate", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Food details fetch successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Food details fetch successfully",
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
    public function index(Request $request)
    {
        try{
            $user_id = Auth::user()->id;
            //$start = $request->startDate;
            
            if(!empty($request->endDate)){
                $start = $request->startDate;
                $end = $request->endDate.' 23:59:00';
            }else{
                //$start = $request->startDate.' 23:59:00';
                $start = $request->startDate;
                $end = '';
            }
            
            // $foodDataList = UserFoodData::where(array('user_id' => $user_id))
            // ->whereBetween('created_at', [$start, $end])
            // ->get();

            $foodDataArr = UserFoodData::where(function ($between) use($start,$end) {
                if(!empty($start) && empty($end)){ 
                    //$between->whereDate(DATE('created_at'), $start);
                    $between->whereDate('created_at', $start);
                    //$between->where('created_at', '<=', $start);  
				}
                
                if(!empty($start) && !empty($end)){
                    $between->whereBetween('created_at',[$start,$end]);
                }
			});
            $foodData = $foodDataArr->where(array('user_id' => $user_id))->get();
            
            // echo $foodData;exit;

            $userFoodData = UserFoodDataResource::collection($foodData);

            //get user exercise data

            $exerciseArr = UserExerciseData::where(function ($query) use($start,$end) {
                if(!empty($start) && empty($end)){ 
                    //$query->where('created_at', '<=', $start); 
                    $query->whereDate('created_at', $start);
				}
                
                if(!empty($start) && !empty($end)){
                    $query->whereBetween('created_at',[$start,$end]);
                }
			});
            $exerciseData = $exerciseArr->where(array('user_id' => $user_id))->get();

            //$exerciseData = UserExerciseData::where(array('user_id' => $user_id))->get();
            $userExerciseData = UserExerciseDataResource::collection($exerciseData);
           
            $data = array(
                'FoodData' => $userFoodData,
                'exerciseData' => $userExerciseData,
            );
            
            return successResponse(trans('api-message.FOOD_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
    /**
        * @OA\Post(
        * path="/api/v1/store-food-data",
        * operationId="store-food-data",
        * tags={"store-food-data"},
        * summary="store-food-data",
        * description="store-food-data",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"FDIID","Protein","Carbohydrate","Calory","Energy","typeOfMeal","DateAndTime","FoodDescription","quantity","foodOrExercise","serving_qty","serving_size","notes"},
        *               @OA\Property(property="FDIID", type="text"),
        *               @OA\Property(property="Protein", type="text"),
        *               @OA\Property(property="Carbohydrate", type="text"),
        *               @OA\Property(property="Calory", type="text"),
        *               @OA\Property(property="Energy", type="text"),
        *               @OA\Property(property="typeOfMeal", type="text"),
        *               @OA\Property(property="DateAndTime", type="text"),
        *               @OA\Property(property="FoodDescription", type="text"),
        *               @OA\Property(property="quantity", type="text"),
        *               @OA\Property(property="foodOrExercise", type="text"),
        *               @OA\Property(property="serving_qty", type="text"),
        *               @OA\Property(property="serving_size", type="text"),
        *               @OA\Property(property="notes", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Food details added successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Food details added successfully.",
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
    public function store(Request $request)
    {
        Log::info('Create user food data');

        // $public = PublicKeyLoader::load(config('constant.RSA_PUBLIC_KEY'));
        //  echo base64_encode($public->encrypt($request->food));exit;
        
        // $private = PublicKeyLoader::load(config('constant.RSA_PRIVATE_KEY'));
        // $jsonData = $private->decrypt(base64_decode($request->food));
        // $person = json_decode($jsonData);
        
        try {
            //Start JSON data
            // $foodData = new UserFoodData();
            // $foodData->user_id = Auth::user()->id;
            // $foodData->fdiid = !empty($person->FDIID) ? $person->FDIID : '';
            // $foodData->protein = !empty($person->Protein) ? $person->Protein : '';
            // $foodData->carbohydrate = !empty($person->Carbohydrate) ? $person->Carbohydrate : '';
            // $foodData->calory = !empty($person->Calory) ? $person->Calory : '';
            // $foodData->energy = !empty($person->Energy) ? $person->Energy : '';
            // $foodData->type_of_meal = !empty($person->typeOfMeal) ? $person->typeOfMeal : '';
            // $foodData->date_and_time = $person->DateAndTime;//!empty($request->DateAndTime) ? $private->decrypt(base64_decode($request->DateAndTime)) : '';
            // $foodData->food_description = !empty($person->FoodDescription) ? $person->FoodDescription : '';
            //$foodData->save();
            //End JSON data
                $foodData = new UserFoodData();
                $foodData->user_id = Auth::user()->id;
                $foodData->fdiid = !empty($request->FDIID) ? $request->FDIID : NULL;
                $foodData->protein = !empty($request->Protein) ? $request->Protein : NULL;
                $foodData->carbohydrate = !empty($request->Carbohydrate) ? $request->Carbohydrate : NULL;
                $foodData->calory = !empty($request->Calory) ? $request->Calory : '';
                $foodData->energy = !empty($request->Energy) ? $request->Energy : NULL;
                $foodData->type_of_meal = !empty($request->typeOfMeal) ? $request->typeOfMeal : '';
                $foodData->date_and_time = $request->DateAndTime;
                $foodData->quantity = $request->quantity;
                $foodData->food_or_exercise = $request->foodOrExercise;
                $foodData->serving_qty = !empty($request->serving_qty) ? $request->serving_qty : '';
                $foodData->serving_size = !empty($request->serving_size) ? $request->serving_size : '';
                $foodData->food_description = !empty($request->FoodDescription) ? $request->FoodDescription : NULL;
                $foodData->notes = !empty($request->notes) ? $request->notes : '';
                $foodData->save();
            Log::info('Add food detail successfully');
            $messge = trans('api-message.ADD_FOOD_DETIAL_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Create Food details :: message :'.$message);
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
        * path="/api/v1/edit-food-data",
        * operationId="edit-food-data",
        * tags={"Edit food data"},
        * summary="Edit food data",
        * description="Edit food data",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"FDIID","Protein","Carbohydrate","Calory","Energy","typeOfMeal","DateAndTime","FoodDescription","quantity","foodOrExercise","serving_qty","serving_size","notes"},
        *               @OA\Property(property="FDIID", type="text"),
        *               @OA\Property(property="Protein", type="text"),
        *               @OA\Property(property="Carbohydrate", type="text"),
        *               @OA\Property(property="Calory", type="text"),
        *               @OA\Property(property="Energy", type="text"),
        *               @OA\Property(property="typeOfMeal", type="text"),
        *               @OA\Property(property="DateAndTime", type="text"),
        *               @OA\Property(property="FoodDescription", type="text"),
        *               @OA\Property(property="quantity", type="text"),
        *               @OA\Property(property="foodOrExercise", type="text"),
        *               @OA\Property(property="serving_qty", type="text"),
        *               @OA\Property(property="serving_size", type="text"),
        *               @OA\Property(property="notes", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Food details updated successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Food details updated successfully",
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
        //dd($request->id);
        try {
                $id = $request->id;
                $foodData['fdiid'] = !empty($request->FDIID) ? $request->FDIID : 0;
                $foodData['protein'] = !empty($request->Protein) ? $request->Protein : 0;
                $foodData['carbohydrate'] = !empty($request->Carbohydrate) ? $request->Carbohydrate : 0;
                $foodData['calory'] = !empty($request->Calory) ? $request->Calory : '';
                $foodData['energy'] = !empty($request->Energy) ? $request->Energy : 0;
                $foodData['type_of_meal'] = !empty($request->typeOfMeal) ? $request->typeOfMeal : '';
                $foodData['date_and_time'] = $request->DateAndTime;
                $foodData['quantity'] = !empty($request->quantity) ? $request->quantity : 0;
                $foodData['food_description'] = !empty($request->FoodDescription) ? $request->FoodDescription : NULL;
                $foodData['food_or_exercise'] = !empty($request->foodOrExercise) ? $request->foodOrExercise : 0;
                $foodData['serving_qty'] = !empty($request->serving_qty) ? $request->serving_qty : 0;
                $foodData['serving_size'] = !empty($request->serving_size) ? $request->serving_size : 0;
                $foodData['notes'] = !empty($request->notes) ? $request->notes : '';
                UserFoodData::where('id', $id)->update($foodData);

                $result = UserFoodData::where(array('id' => $id))->first();
                $userFoodData = new UserFoodDataResource($result);

                $data = array(
                    'FoodData' => $userFoodData,
                );
                Log::info('Edit food detail successfully');
                return successResponse(trans('api-message.FOOD_DETAILS_UPDATED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Edit Food details :: message :'.$message);
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
    /**
        * @OA\Delete(
        * path="/api/v1/delete-food-data/{id}",
        * operationId="delete-food-data",
        * tags={"delete food data"},
        * summary="delete food data",
        * description="delete food data",
         *      @OA\Parameter(
     *          name="id",
     *          description="id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
        *      @OA\Response(
        *          response=201,
        *          description="Food details delete successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Food details delete successfully",
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
    public function destroy($id)
    {
        try {
            Log::info('Delete User food data API started !!');
            $userFood = UserFoodData::find($id);
            if (empty($userFood)) {
                Log::warning('Food detail not found with id:' . $id);
                return errorResponse(trans('api-message.FOOD_DETAILS_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);    
            }
            $userFood->delete();

            Log::info('Delete Food detail with id:' . $id);
            return successResponseWithoutData(trans('api-message.DELETE_FOOD_DETIAL_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Unable to delete Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_DELETE_FOOD_DETAILS'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Get Food Data specified resource from storage.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 15/03/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/get-food-detail",
        * operationId="get food detail",
        * tags={"Get food detail"},
        * summary="Get food detail",
        * description="Get food detail",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"gtin_upc"},
        *               @OA\Property(property="gtin_upc", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Get food Data added successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Get food Data added successfully.",
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
    public function getFoodData(Request $request)
    {
        try {
            $foodDetail = Foods::where('upin_gstin',$request->gtin_upc)->first();
            
            if (empty($foodDetail)) {
                Log::warning('Foods not found with id:' . $request->gtin_upc);
                return errorResponse(trans('api-message.FOODS_NOT_FOUND'), STATUS_CODE_INSUFFICIENT_DATA);
            }
            
            $userFoodData = new FoodsMasterResource($foodDetail);
            $data = array(
                'FoodData' => $userFoodData,
            );

            Log::info('Get foods detail successfully');
            return successResponse(trans('api-message.GET_FOOD_DETAILS_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Edit Food details :: message :'.$message);
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

}
