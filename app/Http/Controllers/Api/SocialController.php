<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\UserRegisterResource;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Null_;

class SocialController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : Social Signup
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 15/02/2022
    */

     /**
        * @OA\Post(
        * path="/api/v1/auth/social-signup",
        * operationId="social-signup",
        * tags={"Social Signup (facebook,google,apple)"},
        * summary="Social Signup (facebook,google,apple)",
        * description="Social Signup (facebook,google,apple)",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"appVersion","deviceType","deviceToken"},
        *               @OA\Property(property="appleId", type="text"),
        *               @OA\Property(property="facebookId", type="text"),
        *               @OA\Property(property="googleId", type="text"),
        *               @OA\Property(property="deviceType", type="text"),
        *               @OA\Property(property="deviceToken", type="text"),
        *               @OA\Property(property="appVersion", type="text"),
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
    public function socialsignup(Request $request)
    {
        try {
                if (!empty($request->facebookId)) {
                    $response = User::where('facebook_id', $request->facebookId)->first();
                    $data['facebook_id'] = $request->facebookId;
                    $data['social_flag'] = 2;
                    $message = trans('api-message.FACEBOOK_ALREADY_EXISTS');
                } else if (!empty($request->googleId)) {
                    $response = User::where('google_id', $request->googleId)->first();
                    $data['google_id'] = $request->googleId;
                    $data['social_flag'] = 1;
                    $message = trans('api-message.GOOGLE_ALREADY_EXISTS');
                } else if (!empty($request->appleId)) {
                    $response = User::where('apple_id', $request->appleId)->first();
                    $data['apple_id'] = $request->appleId;
                    $data['social_flag'] = 3;
                    $message = trans('api-message.APPLE_ALREADY_EXISTS');
                }
                if(empty($response) || $response == null){
                    $data['role'] = 5;
                    $data['appVersion'] = $request->appVersion;
                    $data['status'] = 1;
                    //dd($data);
                    $user = User::create($data);

                    if (isset($request->deviceToken) && !empty($request->deviceToken)) {
                        DeviceToken::where(['user_id' => $user->id])->exists();
                        $deviceData['user_id'] = $user->id;
                        $deviceData['device_type'] = $request->deviceType;
                        $deviceData['device_token'] = $request->deviceToken;
                        DeviceToken::Create($deviceData);
                    }

                    $user_data = User::where('id', $user->id)->with('consumer')->first();
                    //$token = $user_data->createToken('SixPac')->accessToken;
                    $tokenResult= $user->createToken(ACCESS_TOKEN);
                    $token  = $tokenResult->token;
                    $token->expires_at 	= setTokenExpiryTime();
                    $token->refresh_at 	= setTokenRefreshTime();
                    $tokenData = $tokenResult->accessToken;

                    $usersDetail = new RegisterResource($user_data);
                    $data = array(
                        'users' => $usersDetail,
                        'token' => $tokenData
                    );

                    // Commit to DB
                    DB::commit();
                    // All good so return the response
                    return successResponse(trans('api-message.USER_CREATED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
                }else{
                    return errorResponse($message,STATUS_CODE_ERROR);
                }

            } catch (\Exception $e) {
                // Log social login error messages
                Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                    '<Message>' => $e->getMessage(),
                ]));
                return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
            }
        }

    /*
        @Author : Ritesh Rana
        @Desc   : Social Login
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 15/02/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/auth/social-login",
        * operationId="social-login",
        * tags={"Social login (facebook,google,apple)"},
        * summary="Social login (facebook,google,apple)",
        * description="Social login (facebook,google,apple)",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"appleId","deviceType","deviceToken"},
        *               @OA\Property(property="facebookId", type="text"),
        *               @OA\Property(property="googleId", type="text"),
        *               @OA\Property(property="appleId", type="text"),
        *               @OA\Property(property="deviceType", type="text"),
        *               @OA\Property(property="deviceToken", type="text"),
        *               @OA\Property(property="appVersion", type="text"),
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
    public function socialLogin(Request $request)
    {
        try {
            if (!empty($request->facebookId)) {
                $response = User::where('facebook_id', $request->facebookId)->first('id');
                $data['facebook_id'] = $request->facebookId;
                $data['social_flag'] = 2;
                $message = trans('api-message.FACEBOOK_INVALID');
            } else if (!empty($request->googleId)) {
                $response = User::where('google_id', $request->googleId)->first('id');
                $data['google_id'] = $request->googleId;
                $data['social_flag'] = 1;
                $message = trans('api-message.GOOGLE_INVALID');
            } else if (!empty($request->appleId)) {
                $response = User::where('apple_id', $request->appleId)->first('id');
                $data['apple_id'] = $request->appleId;
                $data['social_flag'] = 3;
                $message = trans('api-message.APPLE_INVALID');
            }

            if(!empty($response)){
                $user_data = User::where('id', $response->id)->with('consumer')->first();
                //$token = $user_data->createToken('SixPac')->accessToken;
                $tokenResult= $user_data->createToken(ACCESS_TOKEN);
                $token  = $tokenResult->token;
                $token->expires_at 	= setTokenExpiryTime();
                $token->refresh_at 	= setTokenRefreshTime();
                $tokenData = $tokenResult->accessToken;

                //$usersDetail = new UserRegisterResource($user_data);
                $usersDetail = new RegisterResource($user_data);

                if (isset($request->deviceToken) && !empty($request->deviceToken)) {
                    DeviceToken::where(['user_id' => $user_data->id])->exists();
                    $deviceData['user_id'] = $user_data->id;
                    $deviceData['device_type'] = $request->deviceType;
                    $deviceData['device_token'] = $request->deviceToken;
                    DeviceToken::Create($deviceData);
                }

                $data = array(
                    'users' => $usersDetail,
                    'token' => $tokenData
                );

                // Commit to DB
                DB::commit();
                // All good so return the response
                return successResponse(trans('api-message.USER_CREATED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
            }else{
                return errorResponse($message,STATUS_CODE_ERROR);
            }

        } catch (\Exception $e) {
            // Log social login error messages
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
