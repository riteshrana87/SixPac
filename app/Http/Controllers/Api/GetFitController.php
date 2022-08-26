<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BodyPartsReource;
use App\Http\Resources\ExerciseByInterestReource;
use App\Http\Resources\ExerciseDurationReource;
use App\Http\Resources\GetFitExerciseResource;
use App\Http\Resources\GetfitSearchTypeReource;
use App\Http\Resources\OnDemandServiceReource;
use App\Http\Resources\PlanDayReource;
use App\Http\Resources\PlanGoalsReource;
use App\Http\Resources\WorkoutTypeResource;
use App\Models\BodyParts;
use App\Models\ConsumerInterests;
use App\Models\Exercisables;
use App\Models\Exercise;
use App\Models\ExerciseDuration;
use App\Models\GetfitSearchType;
use App\Models\OnDemandService;
use App\Models\PlanDay;
use App\Models\PlanGoals;
use App\Models\WorkoutCategory;
use App\Models\WorkoutType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GetFitController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : Get Workout Category by getfit section.
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 15/06/2022
    */
    public function getWorkoutType(Request $request)
    {
        try {
            $id = $request->getfitId;
            
            $categorys = WorkoutType::where('getfit_id', $id)->where('status',1)->get();
            $categorysList = WorkoutTypeResource::collection($categorys);
            /*get users details*/
            
            $data = array(
                'workoutType' => $categorysList,
            );
            return successResponse(trans('api-message.GET_WORKOUT_TYPE'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get on demand services by getfit section. (Designated user)
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 15/06/2022
    */
    
    public function getOnDemandServices(Request $request)
    {
        try {
            $id = $request->getfitId;
            /*Get On Demand Service*/
            $categorys = OnDemandService::where('getfit_id', $id)->where('status',1)->get();
            $categorysList = OnDemandServiceReource::collection($categorys);
            
            $data = array(
                'demandServices' => $categorysList,
            );
            return successResponse(trans('api-message.GET_ON_DEMAND_SERVICES'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get body parts
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 15/06/2022
    */
    
    public function getBodyParts()
    {
        try {
            /*Get On Demand Service*/
            $bodyParts = BodyParts::where('status',1)->get();
            $bodyPartsList = BodyPartsReource::collection($bodyParts);
            
            $data = array(
                'bodyParts' => $bodyPartsList,
            );
            return successResponse(trans('api-message.GET_BODY_PARTS'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get on demand services by getfit section. (Designated user)
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 15/06/2022
    */
    
    public function getGetfitSearchType(Request $request)
    {
        try {
            $id = $request->getfitId;
            /*Get On Demand Service*/
            $categorys = GetfitSearchType::where('getfit_id', $id)->where('status',1)->get();
            $categorysList = GetfitSearchTypeReource::collection($categorys);
            
            $data = array(
                'workouts' => $categorysList,
            );
            return successResponse(trans('api-message.GET_ON_DEMAND_SERVICES'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get For You data
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 30/06/2022
    */
    
    public function getForYouData()
    {
        try {
            //$interest = ExerciseByInterestReource::collection($bodyParts);
            $OriginalImagePath = Config::get('constant.BODY_PART_ORIGINAL_PHOTO_UPLOAD_PATH');

            // $interests = ConsumerInterests::where('user_id', Auth::user()->id,)->groupBy('interest_id')->pluck('interest_id')->toArray();
            // $exerciseId = Exercisables::where('exercisable_type','App\Models\Interests')->whereIn('exercisable_id',$interests)->groupBy('exercise_id')->pluck('exercise_id')->toArray();

            // $exerciseList = Exercise::with(['interests','equipments','bodyParts','ageGroups','fitnessLevels','workoutType'])
            //     ->whereIn('id',$exerciseId)
            //     ->where("status",1)
            //     ->orderBy('created_at', 'DESC')
            //     ->get();
            // /*End Create query for Get Exercise data */
            // $exerciseData = GetFitExerciseResource::collection($exerciseList);
            

            $interest = [
                [
                    "id"=> 1,
                    "title"=> "Ab Roll outs",
                    "duration"=> 2,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'abs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 2,
                    "title"=> "Alternating foot jump",
                    "duration"=> 3,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'arms.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 3,
                    "title"=> "Alternating split squat jumps",
                    "duration"=> 4,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'back.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 4,
                    "title"=> "arch legs",
                    "duration"=> 6,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'chest.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 5,
                    "title"=> "Arm Bar Left",
                    "duration"=> 7,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'legs.jpg'),
                    "status"=> 1,
                ]
            ];

            $favoriteCreators = [
                [
                    "id"=> 1,
                    "name"=> "Ab Roll outs",
                    "countExercise"=> 2,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'chest.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 2,
                    "name"=> "Alternating foot jump",
                    "countExercise"=> 1,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'legs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 3,
                    "name"=> "Alternating split squat jumps",
                    "countExercise"=> 3,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'arms.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 4,
                    "name"=> "arch legs",
                    "countExercise"=> 2,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'abs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 5,
                    "name"=> "Arm Bar Left",
                    "countExercise"=> 5,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'back.jpg'),
                    "status"=> 1,
                ]
            ];

            $history = [
                [
                    "id"=> 1,
                    "title"=> "Ab Roll outs",
                    "duration"=> 2,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'shoulders.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 2,
                    "title"=> "Alternating foot jump",
                    "duration"=> 1,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'back.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 3,
                    "title"=> "Alternating split squat jumps",
                    "duration"=> 4,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'upper_body.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 4,
                    "title"=> "arch legs",
                    "duration"=> 3,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'lower_body.jpg'),
                    "status"=> 1,
                ]
            ];


            $featuredCollection = [
                [
                    "id"=> 1,
                    "title"=> "Ab Roll outs",
                    "duration"=> 2,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'shoulders.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 2,
                    "title"=> "Alternating foot jump",
                    "duration"=> 1,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'full_body.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 3,
                    "title"=> "Alternating split squat jumps",
                    "duration"=> 4,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'upper_body.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 4,
                    "title"=> "arch legs",
                    "duration"=> 3,
                    "iconFile"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'lower_body.jpg'),
                    "status"=> 1,
                ]
            ];
           
            $data = array(
                'ExerciseByInterest' => $interest,
                'favoriteCreators' => $favoriteCreators,
                'history' => $history,
                'featuredCollection' => $featuredCollection,
            );
            return successResponse(trans('api-message.GET_FOR_YOU_DATA'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get Data For Exercise tab
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 30/06/2022
    */
    
    public function getExerciseTabData()
    {
        try {
            $OriginalImagePath = Config::get('constant.BODY_PART_ORIGINAL_PHOTO_UPLOAD_PATH');
            $categorys = WorkoutType::where('status',1)->get();
            $byType = WorkoutTypeResource::collection($categorys);
            
            $favoriteCreators = [
                [
                    "id"=> 1,
                    "name"=> "Ab Roll outs",
                    "countExercise"=> 2,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'chest.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 2,
                    "name"=> "Alternating foot jump",
                    "countExercise"=> 1,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'legs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 3,
                    "name"=> "Alternating split squat jumps",
                    "countExercise"=> 3,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'arms.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 4,
                    "name"=> "arch legs",
                    "countExercise"=> 2,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'abs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 5,
                    "name"=> "Arm Bar Left",
                    "countExercise"=> 5,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'back.jpg'),
                    "status"=> 1,
                ]
            ];
            
           
            $bodyParts = BodyParts::where('status',1)->get();
            $bodyPartsList = BodyPartsReource::collection($bodyParts);
            
            $data = array(
                'bodyParts' => $bodyPartsList,
                'byType' => $byType,
                'favoriteCreators' => $favoriteCreators,
            );
            
            return successResponse(trans('api-message.GET_EXERCISE_TAB_DATA'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get Data For Workout tab Data
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 30/06/2022
    */
    
    public function getWorkoutTabData()
    {
        try {
            $OriginalImagePath = Config::get('constant.BODY_PART_ORIGINAL_PHOTO_UPLOAD_PATH');

            $categorys = WorkoutType::where('status',1)->get();
            $byType = WorkoutTypeResource::collection($categorys);
            
            $favoriteCreators = [
                [
                    "id"=> 1,
                    "name"=> "Ab Roll outs",
                    "countExercise"=> 2,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'chest.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 2,
                    "name"=> "Alternating foot jump",
                    "countExercise"=> 1,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'legs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 3,
                    "name"=> "Alternating split squat jumps",
                    "countExercise"=> 3,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'arms.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 4,
                    "name"=> "arch legs",
                    "countExercise"=> 2,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'abs.jpg'),
                    "status"=> 1,
                ],
                [
                    "id"=> 5,
                    "name"=> "Arm Bar Left",
                    "countExercise"=> 5,
                    "profilePic"=> Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.'back.jpg'),
                    "status"=> 1,
                ]
            ];
            
            
            $bodyParts = BodyParts::where('status',1)->get();
            $bodyPartsList = BodyPartsReource::collection($bodyParts);

            $exerciseDuration = ExerciseDuration::where('status',1)->get();
            $exerciseDurationList = ExerciseDurationReource::collection($exerciseDuration);
            
            $data = array(
                'byType' => $byType,
                'exerciseDuration' => $exerciseDurationList,
                'bodyParts' => $bodyPartsList,
                'favoriteCreators' => $favoriteCreators,
            );
            
            return successResponse(trans('api-message.GET_WORKOUT_TAB_DATA'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get Data For Plan tab Data
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 30/06/2022
    */
    
    public function getPlanTabData()
    {
        try {
            $planDay = PlanDay::where('status',1)->get();
            $dayList = PlanDayReource::collection($planDay);

            $planGoals = PlanGoals::where('status',1)->get();
            $planGoalsList = PlanGoalsReource::collection($planGoals);
            
            $data = array(
                'daysPerWeek' => $dayList,
                'planGoals' => $planGoalsList,
            );
            
            return successResponse(trans('api-message.GET_PLAN_TAB_DATA'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

}
