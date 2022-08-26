<?php

namespace App\Http\Controllers\Api\Business;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function __construct()
    {
        $this->businessOriginalImagePath = Config::get('constant.BUSINESS_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->businessThumbImagePath = Config::get('constant.BUSINESS_THUMB_PHOTO_UPLOAD_PATH');
        $this->businessThumbImageHeight = Config::get('constant.BUSINESS_THUMB_PHOTO_HEIGHT');
        $this->businessThumbImageWidth = Config::get('constant.BUSINESS_THUMB_PHOTO_WIDTH');

    }

    /*
        @Author : Ritesh Rana
        @Desc   : Show the form for editing the specified resource.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 21/01/2022
    */
    public function profileDetails()
    {
        try {
            $user = Auth::user();
            //dd($user);
            $users = new UsersResource($user);
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
        @Date   : 21/01/2022
    */
    public function update(Request $request)
    {
        try {
            $id = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'first_name' => 'bail|required|min:2|max:50',
                'last_name' => 'bail|required|min:2|max:50',
                'email' => 'required|email|unique:users,email,'. $id .',id,deleted_at,NULL|max:100',
                'phone' => 'nullable|string|unique:users,phone,'. $id .',id,deleted_at,NULL',
                'gender' => ['nullable', Rule::in([Config::get('constant.MALE'), Config::get('constant.FEMALE')])],
            ]);

            if ($validator->fails()) {
                Log::info('Create Update User details :: message :' . $validator->errors());
                return response()->json(['message' => $validator->errors(), 'status' => 422], 422);
            }

            // Upload User Photo
            if (!empty($request->file('profile_photo')) && $request->file('profile_photo')->isValid()) {
                $params = [
                        'originalPath' => $this->businessOriginalImagePath,
                        'thumbPath' => $this->businessThumbImagePath,
                        'thumbHeight' => $this->businessThumbImageHeight,
                        'thumbWidth' => $this->businessThumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('profile_photo'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    $response = [];
                    $response['message'] = "error";
                    $response['status'] = 500;
                    Log::info(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'));
                    return response()->json($response, 500);
                }

                $input['profile_photo'] = $userPhoto['imageName'];
            }

            $input['first_name'] = $request->first_name;
            $input['last_name'] = $request->last_name;
            $input['email'] = $request->email;
            $input['phone'] = $request->phone != "null" & !empty($request->phone) ? $request->phone : null;
            $input['gender'] = $request->get('gender');
            User::where('id', $id)->update($input);
            Log::info('User details Updated Successfully :: message :General Information updated.');

            $user = User::where('id', $id)->first();
            $users = new UsersResource($user);

            $data = array(
                'users' => $users,
            );
            return response()->json(['message' => 'General Information updated.', 'data' => $data, 'status' => 200], 200);
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return response()->json([
                'status' => 500,
                'message' => trans('api-message.DEFAULT_ERROR_MESSAGE'),
            ], 500);
        }
    }
}
