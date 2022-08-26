<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserRegisterResource;
use App\Models\ConsumerProfileDetail;
use App\Models\User;
use App\Services\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserSettingController extends Controller
{

    public function __construct()
    {
        $this->originalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->thumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->thumbImageHeight = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->thumbImageWidth = Config::get('constant.USER_THUMB_PHOTO_WIDTH');


        $this->bannerOriginalImagePath = Config::get('constant.BANNER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->bannerThumbImagePath = Config::get('constant.BANNER_THUMB_PHOTO_UPLOAD_PATH');
        $this->bannerThumbImageHeight = Config::get('constant.BANNER_THUMB_PHOTO_HEIGHT');
        $this->bannerThumbImageWidth = Config::get('constant.BANNER_THUMB_PHOTO_WIDTH');

    }
     /*
        @Author : Ritesh Rana
        @Desc   : Update the specified resource in storage..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function personalSetting (Request $request)
    {
        try {
            $id = Auth::user()->id;
            $emailverify = User::where('email', $request->email)->whereNotIn('id',[$id])->first();
            if (!empty($emailverify)) {
                Log::info('This Email already exists');
                return errorResponse(trans('api-message.EMAIL_HAS_ALREADY_BEEN_TAKEN'),VALIDATOR_CODE_ERROR);
            }


            $phoneVerify = User::where('phone', $request->phone)->whereNotIn('id',[$id])->first();
            if (!empty($phoneVerify)) {
                Log::info('This phone already exists');
                return errorResponse(trans('api-message.PHONE_HAS_ALREADY_BEEN_TAKEN'),VALIDATOR_CODE_ERROR);
            }
                

            // Upload User Photo
            if (!empty($request->file('avtar')) && $request->file('avtar')->isValid()) {
                $params = [
                        'originalPath' => $this->originalImagePath,
                        'thumbPath' => $this->thumbImagePath,
                        'thumbHeight' => $this->thumbImageHeight,
                        'thumbWidth' => $this->thumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('avtar'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $input['avtar'] = $userPhoto['imageName'];
            }

            // Upload User Photo
            if (!empty($request->file('banner_pic')) && $request->file('banner_pic')->isValid()) {
                $params = [
                        'originalPath' => $this->bannerOriginalImagePath,
                        'thumbPath' => $this->bannerThumbImagePath,
                        'thumbHeight' => $this->bannerThumbImageHeight,
                        'thumbWidth' => $this->bannerThumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('banner_pic'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $input['banner_pic'] = $userPhoto['imageName'];
            }
            $userData = User::where('id', $id)->first();
            
            // $input['name'] = !empty($request->full_name) ? $request->full_name : $userData->name;
            // $input['email'] = !empty($request->email) ? $request->email : $userData->email;
            // $input['phone'] = !empty($request->phone) ? $request->phone : $userData->phone;
            // $input['date_of_birth'] = !empty($request->date_of_birth) ? $request->date_of_birth : $userData->date_of_birth;
            // $input['gender_pronoun'] = !empty($request->genderPronoun) ? $request->genderPronoun : $userData->gender_pronoun;

             $input['name'] = !empty($request->full_name) ? $request->full_name : $userData->name;
            $input['email'] = isset($request->email) ? $request->email : NULL;
            $input['phone'] = !empty($request->phone) ? $request->phone : $userData->phone;
            $input['date_of_birth'] = !empty($request->date_of_birth) ? $request->date_of_birth : $userData->date_of_birth;
            $input['gender_pronoun'] = !empty($request->genderPronoun) ? $request->genderPronoun : $userData->gender_pronoun;
            User::where('id', $id)->update($input);

            $consumerDetail = ConsumerProfileDetail::where('user_id', $id)->first();

            if(empty($consumerDetail)){
                $consumer['user_id'] = $id;
                $consumer['country'] = isset($request->country) ? $request->country : NULL;
                $consumer['state'] = isset($request->state) ? $request->state : NULL;
                $consumer['city'] = isset($request->city) ? $request->city : NULL;
                $consumer['zipcode'] = !empty($request->zipcode) ? $request->zipcode : NULL;
                $consumer['height'] = !empty($request->height) ? $request->height : NULL;
                $consumer['weight'] = !empty($request->weight) ? $request->weight : NULL;
                
//                $consumer['weight_goal'] = !empty($request->weight_goal) ? $request->weight_goal : NULL;
                ConsumerProfileDetail::create($consumer);
                Log::info('User personal setting details Add Successfully :: message :Personal setting Information updated.');
                $messge = trans('api-message.PERSONAL_SETTING_INFORMATION_ADDED_SUCCESSFULLY');
            }else{
                $profileData = ConsumerProfileDetail::where('user_id', $id)->first();
                $UpdateCon['country'] =  isset($request->country) ? $request->country : NULL;
                $UpdateCon['state'] =  isset($request->state) ? $request->state : NULL;
                $UpdateCon['city'] =  isset($request->city) ? $request->city : NULL;
                $UpdateCon['zipcode'] = !empty($request->zipcode) ? $request->zipcode : $profileData->zipcode;
                $UpdateCon['height'] = !empty($request->height) ? $request->height : $profileData->height;
                $UpdateCon['weight'] = !empty($request->weight) ? $request->weight : $profileData->weight;
                //$UpdateCon['weight_goal'] = !empty($request->weight_goal) ? $request->weight_goal : $profileData->weight_goal;
                ConsumerProfileDetail::where('user_id', $id)->update($UpdateCon);
                Log::info('User personal setting details Updated Successfully :: message :Personal setting updated.');
                $messge = trans('api-message.PERSONAL_SETTING_INFORMATION_UPDATED_SUCCESSFULLY');
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
        @Desc   : Upload Profile And Banner
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/04/2022
    */
    public function uploadProfileAndBanner(Request $request)
    {
        try {
            $id = Auth::user()->id;
            // Upload User Photo
            if (!empty($request->file('avtar')) && $request->file('avtar')->isValid()) {
                $params = [
                        'originalPath' => $this->originalImagePath,
                        'thumbPath' => $this->thumbImagePath,
                        'thumbHeight' => $this->thumbImageHeight,
                        'thumbWidth' => $this->thumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('avtar'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $avtar['avtar'] = $userPhoto['imageName'];
                User::where('id', $id)->update($avtar);
            }

            // Upload User Photo
            if (!empty($request->file('banner_pic')) && $request->file('banner_pic')->isValid()) {
                $params = [
                        'originalPath' => $this->bannerOriginalImagePath,
                        'thumbPath' => $this->bannerThumbImagePath,
                        'thumbHeight' => $this->bannerThumbImageHeight,
                        'thumbWidth' => $this->bannerThumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('banner_pic'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $banner['banner_pic'] = $userPhoto['imageName'];
                User::where('id', $id)->update($banner);
            }
            
            $messge = trans('api-message.IMAGE_UPLOAD_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
