<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsumerBodyDetailResource;
use App\Http\Resources\ConsumerDetailResource;
use App\Http\Resources\UserPostResource;
use App\Http\Resources\UserRegisterResource;
use App\Http\Resources\UsersResource;
use App\Models\ConsumerInterests;
use App\Models\ConsumerProfileDetail;
use App\Models\User;
use App\Models\UserPost;
use App\Services\ImageUpload;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function __construct()
    {
        $this->businessOriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->businessThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->businessThumbImageHeight = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->businessThumbImageWidth = Config::get('constant.USER_THUMB_PHOTO_WIDTH');

    }
    /*
        @Author : Ritesh Rana
        @Desc   : Show the form for editing the specified resource.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function profileDetails()
    {
        try {
            $id = Auth::user()->id;
            $user = User::where('id', $id)->with('consumer')->first();
            $users = new UserRegisterResource($user);
            /*get users details*/
            $data = array(
                'users' => $users,
            );
            return successResponse(trans('api-message.GET_PROFILE_DETAIL'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Update the specified resource in storage..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function update(Request $request)
    {
       // dd($request->all());
        try {
            $id = Auth::user()->id;

            // $validator = Validator::make($request->all(), [
            //     'name' => 'bail|required|min:2|max:50',
            //     'user_name' => 'bail|required|min:2|max:50',
            //     'preferred_pronoun' => 'required',
            //     'isLocation' => 'required',
            //     'lat' => 'required',
            //     'long' => 'required',
            //     'height' => 'required',
            //     'weight' => 'required',
            //     'gender' => 'required',
            //     'date_of_birth'=> 'required',
            //     'activity_level' => 'required',
            //     'zipcode' => 'required',
            // ]);


            // if ($validator->fails()) {
            //     Log::info('Add/Update consumer User Profile details :: message :' . $validator->errors());
            //     return errorResponse($validator->errors(),VALIDATOR_CODE_ERROR);
            // }

            // Upload User Photo
            if (!empty($request->file('avtar')) && $request->file('avtar')->isValid()) {
                $params = [
                        'originalPath' => $this->businessOriginalImagePath,
                        'thumbPath' => $this->businessThumbImagePath,
                        'thumbHeight' => $this->businessThumbImageHeight,
                        'thumbWidth' => $this->businessThumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('avtar'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $input['avtar'] = $userPhoto['imageName'];
            }
            $userData = User::where('id', $id)->first();

            $input['date_of_birth'] = !empty($request->date_of_birth) ? $request->date_of_birth : $userData->date_of_birth;
            $input['gender'] = !empty($request->gender) ? $request->gender : $userData->gender;
            $input['name'] = !empty($request->name) ? $request->name : $userData->name;
            $input['user_name'] = !empty($request->user_name) ? $request->user_name : $userData->user_name;
            $input['quick_blox_id'] = !empty($request->quickBloxId) ? $request->quickBloxId : $userData->quick_blox_id;
            $input['gender_pronoun'] = !empty($request->genderPronoun) ? $request->genderPronoun : $userData->gender_pronoun;
            User::where('id', $id)->update($input);

            $consumerDetail = ConsumerProfileDetail::where('user_id', $id)->first();
            if(empty($consumerDetail)){
                $consumer['user_id'] = $id;
                $consumer['city'] = !empty($request->city) ? $request->city : NULL;
                $consumer['state'] = !empty($request->state) ? $request->state : NULL;
                $consumer['country'] = !empty($request->country) ? $request->country : NULL;
                //$consumer['preferred_pronoun'] = !empty($request->preferred_pronoun) ? $request->preferred_pronoun : NULL;

                $consumer['location_status'] = !empty($request->isLocation) ? $request->isLocation : 0;
                $consumer['latitude'] = !empty($request->lat) ? $request->lat : NULL;
                $consumer['longitude'] = !empty($request->long) ? $request->long : NULL;
                $consumer['height'] = !empty($request->height) ? $request->height : NULL;
                $consumer['weight'] = !empty($request->weight) ? $request->weight : NULL;
                $consumer['activity_level'] = !empty($request->activity_level) ? $request->activity_level : NULL;
                $consumer['target_weight'] = !empty($request->targetWeight) ? $request->targetWeight : NULL;
                $consumer['starting_weight'] = !empty($request->startingWeight) ? $request->startingWeight : NULL;
                $consumer['activity_frequency'] = !empty($request->acitivityFrequency) ? $request->acitivityFrequency : NULL;
                $consumer['weight_gain_loss_frequency'] = !empty($request->weightGainLossFrequency) ? $request->weightGainLossFrequency : NULL;
                $consumer['weight_goal'] = !empty($request->weightGoalRaw) ? $request->weightGoalRaw : NULL;
                $consumer['zipcode'] = !empty($request->zipcode) ? $request->zipcode : NULL;
                $consumer['daily_calories'] = !empty($request->dailyCalories) ? $request->dailyCalories : NULL;
                $consumer['burned_calory'] = !empty($request->burnedCalory) ? $request->burnedCalory : NULL;
                $consumer['goal_completion_date'] = !empty($request->goalCompletionDate) ? $request->goalCompletionDate : NULL;
                $consumer['measurement_type'] = !empty($request->MeasurementType) ? $request->MeasurementType : NULL;
                $consumer['update_data'] = 1;

                ConsumerProfileDetail::create($consumer);
                Log::info('Consumer User details Add Successfully :: message :General Information updated.');
                $messge = trans('api-message.PROFILE_INFORMATION_ADDED_SUCCESSFULLY');
            }else{
            // dd($request->preferred_pronoun);
                $profileData = ConsumerProfileDetail::where('user_id', $id)->first();
                $UpdateCon['preferred_pronoun'] = !empty($request->preferred_pronoun) ? $request->preferred_pronoun : $profileData->preferred_pronoun;
                $UpdateCon['location_status'] = !empty($request->isLocation) ? $request->isLocation : $profileData->location_status;

                $UpdateCon['city'] =  !empty($request->city) ? $request->city : $profileData->city;
                $UpdateCon['state'] =  !empty($request->state) ? $request->state : $profileData->state;
                $UpdateCon['country'] =  !empty($request->country) ? $request->country : $profileData->country;

                $UpdateCon['latitude'] =  !empty($request->lat) ? $request->lat : $profileData->latitude;
                $UpdateCon['longitude'] = !empty($request->long) ? $request->long : $profileData->longitude;
                $UpdateCon['height'] = !empty($request->height) ? $request->height : $profileData->height;
                $UpdateCon['weight'] = !empty($request->weight) ? $request->weight : $profileData->weight;
                $UpdateCon['activity_level'] = !empty($request->activity_level) ? $request->activity_level : $profileData->activity_level;
                $UpdateCon['target_weight'] = !empty($request->targetWeight) ? $request->targetWeight : $profileData->target_weight;
                $UpdateCon['starting_weight'] = !empty($request->startingWeight) ? $request->startingWeight : $profileData->starting_weight;
                $UpdateCon['activity_frequency'] = !empty($request->acitivityFrequency) ? $request->acitivityFrequency : $profileData->activity_frequency;
                $UpdateCon['weight_gain_loss_frequency'] =  !empty($request->weightGainLossFrequency) ? $request->weightGainLossFrequency : $profileData->weight_gain_loss_frequency;
                $UpdateCon['weight_goal'] = !empty($request->weightGoalRaw) ? $request->weightGoalRaw : $profileData->weight_goal;
                $UpdateCon['zipcode'] = !empty($request->zipcode) ? $request->zipcode : $profileData->zipcode;

                $UpdateCon['daily_calories'] = isset($request->dailyCalories) && $request->dailyCalories >= 0 ? $request->dailyCalories : $profileData->daily_calories;
                $UpdateCon['burned_calory'] = isset($request->burnedCalory) && $request->burnedCalory >= 0 ? $request->burnedCalory : $profileData->burned_calory;
                
                $UpdateCon['goal_completion_date'] = !empty($request->goalCompletionDate) ? $request->goalCompletionDate : $profileData->goal_completion_date;
                $UpdateCon['measurement_type'] = !empty($request->MeasurementType) ? $request->MeasurementType : $profileData->measurement_type;
                $UpdateCon['update_data'] = 1;
                ConsumerProfileDetail::where('user_id', $id)->update($UpdateCon);
                Log::info('Consumer User details Updated Successfully :: message :General Information updated.');
                $messge = trans('api-message.PROFILE_INFORMATION_UPDATED_SUCCESSFULLY');
            }

            $user = User::where('id', $id)->with('consumer')->first();
            $users = new UserRegisterResource($user);

            $data = array(
                'users' => $users,
            );
            return successResponse($messge, STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Update Fitness Status.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function updateFitnessStatus(Request $request)
    {
        try {
            $id = Auth::user()->id;
            // Upload Fitness Status
            $consumerDetail = ConsumerProfileDetail::where('user_id', $id)->first();
            if(empty($consumerDetail)){
                $consumer['user_id'] = $id;
                $consumer['fitness_status'] = $request->fitness_status;
                $consumer['update_data'] = 1;
                ConsumerProfileDetail::create($consumer);
                Log::info('User fitness status details Add Successfully :: message :General Information updated.');
                $messge = trans('api-message.FITNESS_STATUS_DETAILS_ADD_SUCCESSFULLY');
            }else{
                $UpdateCon['fitness_status'] = $request->fitness_status;
                $UpdateCon['update_data'] = 1;
                ConsumerProfileDetail::where('user_id', $id)->update($UpdateCon);
                Log::info('User fitness status details updated successfully :: message :General Information updated.');
                $messge = trans('api-message.FITNESS_STATUS_DETAILS_UPDATE_SUCCESSFULLY');
            }

            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
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
        @Desc   : User profile details with follower and following user count.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 24/02/2022
    */
    public function userMyProfile(Request $request)
    {
        try {
            $id = Auth::user()->id;
            Log::info('Start User profile');
            $user = User::where('id', $id)->with('consumer')->first();
            $users = new UsersResource($user);

            if (!empty($users)) {
                //get user all post data
                $postdatas =  UserPost::getAllUsersPost(Auth::user()->id);
                $postdata = UserPostResource::collection($postdatas);

                //Total Count of login Users Post
                $data['countpost'] = count($postdata);

                //Count of users followers and followings
                $countfollowing = auth()->user()->following()->count();
                $countfollower = auth()->user()->followers()->count();

                Log::info('Users Profile Details');
                $data = array(
                    'following' => $countfollowing,
                    'follower' => $countfollower,
                    'users' => $users,
                    //'postdata' => $postdata,
                );
                return successResponse(trans('api-message.GET_USER_DATA_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get All User profile details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 14/04/2022
    */
    public function getAllUsers(Request $request)
    {
        $id = Auth::user()->id;
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
            Log::info('Start User profile');
            
            /* start count user list*/
              $usersArr = User::with('consumer')->where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('name', 'like', '%' . $search_keyword . '%')
					->orWhere('user_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $userCount = $usersArr->where(array('otp_verified' => 1, 'status' => 1))
                        ->whereNotIn('id',[$id])
                        ->get();
            $numrows = count($userCount);
            /* End count user list*/
            
            /* start get user list*/
            $usersIdArr = User::with('consumer')->where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('name', 'like', '%' . $search_keyword . '%')
					->orWhere('user_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $user = $usersIdArr->where(array('otp_verified' => 1, 'status' => 1))
                ->whereNotIn('id',[$id])
                ->orderBy('name', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();

                //dd($user);
            /* End Get user list*/    

            $users = UsersResource::collection($user);

            $nextPage = LengthPager::makeLengthAware($users, $numrows, $perpage, []);

            if (!empty($users)) {
                Log::info('Users Profile Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'users' => $users
                );
                return successResponse(trans('api-message.GET_USER_DATA_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Show the form for editing the specified resource.
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 22/04/2022
    */
    public function getUserProfileDetails(Request $request)
    {
        try {
            $id = $request->user_id;
            $user = User::where('id', $id)->with('consumer')->first();
            $users = new UserRegisterResource($user);
            /*get users details*/

            //Count of users followers and followings
            $countfollowing = $user->following()->count();
            $countfollower = $user->followers()->count();
            
            $data = array(
                'following' => $countfollowing,
                'follower' => $countfollower,
                'users' => $users,
            );
            return successResponse(trans('api-message.GET_PROFILE_DETAIL'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


}
