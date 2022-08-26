<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\GetUsersDetailsResource;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\UserRegisterResource;
use App\Http\Resources\UsersResource;
use App\Models\ConsumerProfileDetail;
use App\Models\DeviceToken;
use App\Models\Key;
use App\Models\OtpVerified;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
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
        @Desc   : Store a newly created resource in storage..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/01/2022
    */

    /**
        * @OA\Post(
        * path="/api/v1/register",
        * operationId="Check Email",
        * tags={"Check Email"},
        * summary="Check Email",
        * description="Check Email here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"email"},
        *               @OA\Property(property="email", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Data does not exist.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="This email id is already used, Please Login",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */

    public function register(Request $request)
    {
        // Start DB Transaction
        //dd($request->all());
        DB::beginTransaction();
        try {
            
            $userName = User::where('email', $request->email)->get()->toArray();
            if (!empty($userName)) {
                Log::info('This Email already exists');
                return errorResponse(trans('api-message.EMAIL_HAS_ALREADY_BEEN_TAKEN'),VALIDATOR_CODE_ERROR);
            }else{
                return successResponseWithoutData(trans('api-message.DATA_DOES_NOT_EXIST'),STATUS_CODE_SUCCESS);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    public function registerOld(Request $request)
    {
        // Start DB Transaction
        //dd($request->all());
        DB::beginTransaction();
        try {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email|unique:users',
                    'password' => 'required|min:6|max:20',
                ]);

            if ($validator->fails()) {
                Log::info('User Validator Fails');
                return errorResponse(trans('api-message.EMAIL_HAS_ALREADY_BEEN_TAKEN'),VALIDATOR_CODE_ERROR);
            }

            $userName = User::where('email', $request->email)->get()->toArray();
            if (!empty($userName)) {
                Log::info('This Email already exists');
                return successResponseWithoutData(trans('api-message.EMAIL_HAS_ALREADY_BEEN_TAKEN'),STATUS_CODE_SUCCESS);
            }
            // Add user
            $data['role'] = 5;
            $data['app_version'] = $request->appVersion;
            $data['password'] = bcrypt($request->password);
            $data['email'] = $request->email;
            $data['status'] = 0;
            $user = User::create($data);


            if (isset($request->fcm_token) && !empty($request->fcm_token)) {
                $dev = DeviceToken::where(['user_id' => $user->id])->exists();
                $deviceData['user_id'] = $user->id;
                $deviceData['device_type'] = $request->deviceType;
                $deviceData['device_token'] = $request->deviceToken;
                DeviceToken::Create($deviceData);
            }

            // $dataOtp['otp'] = rand(0, 999999);;
            // $dataOtp['user_id'] = $user->id;
            // OtpVerified::create($dataOtp);

            Log::info('user data store in database');
             $tokenResult= $user->createToken(ACCESS_TOKEN);
            $token  = $tokenResult->token;
            $token->expires_at 	= setTokenExpiryTime();
            $token->refresh_at 	= setTokenRefreshTime();
            $tokenData = $tokenResult->accessToken;

            //$data['password'] = $request->password;
           // Log::info('start send email');

            // if (!empty($request->email)) {
            //     $this->sendRegisterMail($data);
            // }
            //$user_data = User::findOrFail($user->id);
            $user_data = User::where('id', $user->id)->with('consumer')->first();
            $usersDetail = new UserRegisterResource($user_data);
            Log::info('end send email');

            $data = array(
                'users' => $usersDetail,
            );

            // Commit to DB
            DB::commit();
            // All good so return the response

            return successResponse(trans('api-message.USER_CREATED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /**
        * @OA\Post(
        * path="/api/v1/generate-otp",
        * operationId="generate-otp",
        * tags={"generate-otp"},
        * summary="User generate-otp",
        * description="User generate-otp here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"mobile"},
        *               @OA\Property(property="mobile", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="OTP has been generated successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="The mobile has already been taken.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function generateOTP(Request $request)
    {
        // Start DB Transaction
        //dd($request->all());
        DB::beginTransaction();
        try {
                $validator = Validator::make($request->all(), [
                    'mobile' => 'required',
                ]);

            if ($validator->fails()) {
                Log::info('User Validator Fails');
                return errorResponse($validator->errors(),VALIDATOR_CODE_ERROR);
            }

            $userName = User::where('phone', $request->mobile)->get()->toArray();
            if (!empty($userName)) {
                Log::info('This mobile already exists');
                return errorResponse(trans('api-message.MOBILE_HAS_ALREADY_BEEN_TAKEN'),VALIDATOR_CODE_ERROR);
            }
            
            // Add user
            $dataOtp['otp'] = rand(100000, 999999);
            // $dataOtp['user_id'] = $request->userId;
            // $dataOtp['email'] = $request->email;
             $dataOtp['mobile'] = $request->mobile;
            //$dataOtp['is_email'] = $request->isEmail;
            OtpVerified::create($dataOtp);

            // $input['otp_verified'] = 0;
            // $input['phone'] = $request->mobile;
            // User::where('id', $request->userId)->update($input);
            Log::info('start send email');

            // if (!empty($request->email)) {
            //     $this->sendRegisterMail($data);
            // }
            // $user_data = User::findOrFail($request->userId);
            // $usersDetail = new UserRegisterResource($user_data);

            Log::info('end send email');
            $data = [
                'OTP' => $dataOtp['otp'],
            ];

            // Commit to DB
            DB::commit();
            // All good so return the response
            return successResponse(trans('api-message.OTP_CREATED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

/**
        * @OA\Post(
        * path="/api/v1/verify-otp",
        * operationId="verify-otp",
        * tags={"verify-otp"},
        * summary="verify otp",
        * description="verify otp",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"OTP","deviceType","email","password","deviceToken"},
        *               @OA\Property(property="OTP", type="text"),
        *               @OA\Property(property="deviceType", type="text"),
        *               @OA\Property(property="email", type="text"),
        *               @OA\Property(property="password", type="text"),
        *               @OA\Property(property="deviceToken", type="text"),
        *               @OA\Property(property="appVersion", type="text"),
        *               @OA\Property(property="facebookId", type="text"),
        *               @OA\Property(property="googleId", type="text"),
        *               @OA\Property(property="appleId", type="text"),
        *               @OA\Property(property="mobile", type="text"),
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
        *      @OA\Response(response=500, description="Your otp has not been verified."),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Whoops! Something went wrong. Please try again."),
        * )
        */
    public function verifyOTP(Request $request)
    {
        //dd($request->all());
        // Start DB Transaction
        DB::beginTransaction();
        try {
                $validator = Validator::make($request->all(), [
                    'OTP' => 'required',
                ]);

            if ($validator->fails()) {
                Log::info('User Validator Fails');
                return errorResponse($validator->errors(),VALIDATOR_CODE_ERROR);
            }

            $getotp = OtpVerified::where('otp',$request->OTP)->where('mobile',$request->mobile)->where('status',1)->orderBy('created_at', 'desc')->first();
            
            if(!empty($getotp)){
                
                // $input['otp_verified'] = 1;
                // $input['is_mobile_verified'] = 1;
                // User::where('id', $request->userId)->update($input);
                $inputOtp['status'] = 0;
                OtpVerified::where('id', $getotp->id)->update($inputOtp);
                
                $response = "";
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
                
                if(!empty($response) || $response != null){
                    return errorResponse($message,STATUS_CODE_ERROR);
                }
                
                // Add user
                $data['role'] = 5;
                $data['app_version'] = !empty($request->appVersion) ? $request->appVersion : '';
                $data['password'] = !empty($request->password) ? bcrypt($request->password) : '';
                $data['email'] = !empty($request->email) ? $request->email : null;
                $data['phone'] = !empty($request->mobile) ? $request->mobile : '';
                $data['status'] = 1;
                $data['otp_verified'] = 1;
                $data['is_mobile_verified'] = 1;
                $user = User::create($data);
                

            if (isset($request->fcm_token) && !empty($request->fcm_token)) {
                $dev = DeviceToken::where(['user_id' => $user->id])->exists();
                $deviceData['user_id'] = $user->id;
                $deviceData['device_type'] = $request->deviceType;
                $deviceData['device_token'] = $request->deviceToken;
                DeviceToken::Create($deviceData);
            }

            // if(isset($request->goalCompletionDate) && !empty($request->goalCompletionDate)){
            //     $consumer['goal_completion_date'] = !empty($request->goalCompletionDate) ? $request->goalCompletionDate : '';
            //     $consumer['user_id'] = $user->id;
            //     ConsumerProfileDetail::where('user_id', $user->id)->Create($consumer);
            // }
            
            //create token 
            
            Log::info('user data store in database');
            $tokenResult= $user->createToken(ACCESS_TOKEN);
        //     echo '<pre>';
        //    print_r($tokenResult);exit;
            $token  = $tokenResult->token;
            $token->expires_at 	= setTokenExpiryTime();
            $token->refresh_at 	= setTokenRefreshTime();
            $tokenData = $tokenResult->accessToken;

            $user_data = User::where('id', $user->id)->with('consumer')->first();
            $usersDetail = new RegisterResource($user_data);
            
            //$usersDetail = new UserRegisterResource($user_data);

            
            Log::info('end send email');

            $data = array(
                'users' => $usersDetail,
                'token' => $tokenData,
            );
            // Commit to DB
            DB::commit();
                // All good so return the response
                //return successResponse(trans('api-message.OTP_VERIFIED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
                return successResponse(trans('api-message.USER_CREATED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
            }else{
                return errorResponse(trans('api-message.OTP_NOT_VERIFIED'),STATUS_CODE_ERROR);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    public function verifyOTPOld(Request $request)
    {
        //dd($request->all());
        // Start DB Transaction
        DB::beginTransaction();
        try {
                $validator = Validator::make($request->all(), [
                    'userId'=> 'required',
                    'isEmail' => 'required',
                    'OTP' => 'required',
                ]);

            if ($validator->fails()) {
                Log::info('User Validator Fails');
                return errorResponse($validator->errors(),VALIDATOR_CODE_ERROR);
            }

            $getotp = OtpVerified::where('otp',$request->OTP)->where('user_id',$request->userId)->where('status',1)->orderBy('created_at', 'desc')->first();
            if(!empty($getotp)){
                $input['otp_verified'] = 1;
                if($request->isEmail == 0){
                    $input['is_mobile_verified'] = 1;
                }else{
                    $input['is_email_verified'] = 1;
                }
                User::where('id', $request->userId)->update($input);
                $inputOtp['status'] = 0;
                OtpVerified::where('id', $getotp->id)->update($inputOtp);
                $data = [
                    'isEmail' => $request->isEmail,
                ];

                // Commit to DB
                DB::commit();
                // All good so return the response
               return successResponse(trans('api-message.OTP_VERIFIED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
            }else{
                return errorResponse(trans('api-message.OTP_NOT_VERIFIED'),STATUS_CODE_ERROR);
            }


        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    /*
        @Author : Ritesh Rana
        @Desc   : Get the needed authorization credentials from the request
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/01/2022
    */

    /**
        * @OA\Post(
        * path="/api/v1/login",
        * operationId="authLogin",
        * tags={"Login"},
        * summary="User Login",
        * description="Login User Here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"email", "password"},
        *               @OA\Property(property="email", type="email"),
        *               @OA\Property(property="password", type="password")
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6|max:20',
        ]);

        if ($validator->fails()) {
            Log::info('User Validator Fails');
            return errorResponse(trans('api-message.EMAIL_PASSWORD_REQUIRED'),VALIDATOR_CODE_ERROR);
        }

        Log::info('User login :: email id:' . request('email'));
        $login_data = Auth::attempt(['email' => request('email'), 'password' => request('password'), 'status' => 1, 'deleted_at' => NULL]);

        if ($login_data == true) {
            $user_id = Auth::user()->id;
            $user = User::where('id', $user_id)->with('consumer')->first();
            $users = new UserRegisterResource($user);
            $tokenResult= $user->createToken(ACCESS_TOKEN);
            $token  = $tokenResult->token;
            $token->expires_at 	= setTokenExpiryTime();
            $token->refresh_at 	= setTokenRefreshTime();
            $tokenData = $tokenResult->accessToken;
            
            /*start add and update device token using FCM*/
            //$dev = DeviceToken::where(['user_id' => $user->id])->exists();
            if (isset($request->deviceToken) && !empty($request->deviceToken)) {
                $data['user_id'] = $user_id;
                $data['device_type'] = $request->deviceType;
                $data['device_token'] = $request->deviceToken;
                DeviceToken::Create($data);
            }
            /*get users details*/
            $data = array(
                'users' => $users,
                'token' => $tokenData,
            );

            Log::info('You are successfully logged in');
            // All good so return the response
            return successResponse(trans('api-message.LOGIN_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
        } else {
            Log::info('User details Unauthorised');
            return errorResponse(trans('api-message.INVALID_LOGIN'),STATUS_CODE_ERROR);
        }
    }


    /*
        @Author : Ritesh Rana
        @Desc   : Get the needed authorization credentials from the request
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 09/02/2022
    */

    /**
        * @OA\Post(
        * path="/api/v1/login-with-mobile",
        * operationId="login-with-mobile",
        * tags={"Login With Mobile"},
        * summary="Login With Mobile",
        * description="Login With Mobile",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"mobileNumber", "OTP"},
        *               @OA\Property(property="mobileNumber", type="text"),
        *               @OA\Property(property="OTP", type="text")
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Login Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="You are successfully logged in.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Your otp has not been verified."),
        * )
        */

    public function loginWithMobile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'mobileNumber' => 'required',
            'OTP' => 'required',
        ]);

        if ($validator->fails()) {
            Log::info('User Validator Fails');
            return errorResponse(trans('api-message.MOBILE_OTP_REQUIRED'),VALIDATOR_CODE_ERROR);
        }
        Log::info('User login :: phone:' . request('mobileNumber'));
        $login_data = User::where(['phone' => request('mobileNumber'),'status' => 1, 'deleted_at' => NULL])->with('consumer')->first();
        if ($login_data == true) {
            $user = $login_data;
           // $request->OTP
           // $OtpData = OtpVerified::where(['otp' => $request->OTP,'user_id' => $user->id,'status'=>1])->first();

           $OtpData = OtpVerified::where(['otp' => $request->OTP,'mobile' => $request->mobileNumber,'status'=>1])->first();

            if(!empty($OtpData)){
                $input['otp_verified'] = 1;
                User::where('id', $user->id)->update($input);

                $inputOtp['status'] = 0;
                OtpVerified::where('id', $OtpData->id)->update($inputOtp);

                if (isset($request->deviceToken) && !empty($request->deviceToken)) {
                    $data['user_id'] = $user->id;
                    $data['device_type'] = $request->deviceType;
                    $data['device_token'] = $request->deviceToken;
                    DeviceToken::Create($data);
                }
               // $users = new UsersResource($user);
                //$user = User::where('id', $user_id)->with('consumer')->first();
                //$users = new UserRegisterResource($user);
                $users = new RegisterResource($user);

                
                
                $tokenResult= $user->createToken(ACCESS_TOKEN);
                $token  = $tokenResult->token;
                $token->expires_at 	= setTokenExpiryTime();
                $token->refresh_at 	= setTokenRefreshTime();
                $tokenData = $tokenResult->accessToken;
                

                /*get users details*/
                $data = array(
                    'users' => $users,
                    'token' => $tokenData,
                );

                Log::info('You are successfully logged in');
                return successResponse(trans('api-message.LOGIN_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
            }else{
                return errorResponse(trans('api-message.OTP_NOT_VERIFIED'),STATUS_CODE_NOT_FOUND);
            }
        } else {

            Log::info('User details Unauthorised');
            return errorResponse(trans('api-message.INVALID_MOBILE'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Logout request
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/01/2022
    */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $deleteRec = DeviceToken::where('device_token',$request->device_token);
        $deleteRec->delete();
        $token->revoke();
        $response = 'you have been successfully logged out!';
        return response()->json(['message' => $response, 'status' => 200], 200);
    }

    /*
        @Author : Ritesh Rana
        @Desc   : generate OTP
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/01/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/without-login-generate-otp",
        * operationId="without-login-generate-otp",
        * tags={"Without Login Generate Otp"},
        * summary="Without Login Generate Otp",
        * description="Without Login Generate Otp",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"mobile"},
        *               @OA\Property(property="mobile", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="OTP has been generated successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="OTP has been generated successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="This Mobile number is not register,Please signup"),
        * )
        */
    public function WithOutLoginGenerateOTP(Request $request)
    {
        // Start DB Transaction

        DB::beginTransaction();
        try {
                $validator = Validator::make($request->all(), [
                    'mobile' => 'required',
                ]);

            if ($validator->fails()) {
                Log::info('User Validator Fails');
                return errorResponse(trans('api-message.MOBILE_REQUIRED'),VALIDATOR_CODE_ERROR);
            }
            $userData = User::where('phone', $request->mobile)->first();

            if(!empty($userData)){
                // Add user
                $dataOtp['otp'] = rand(100000, 999999);
                $dataOtp['user_id'] = $userData->id;
                $dataOtp['email'] = '';
                $dataOtp['mobile'] = $request->mobile;
                OtpVerified::create($dataOtp);

                $input['otp_verified'] = 0;
                User::where('id', $request->userId)->update($input);

                $data = [
                    'OTP' => $dataOtp['otp'],
                ];

                // Commit to DB
                DB::commit();
                // All good so return the response
                return successResponse(trans('api-message.OTP_CREATED_SUCCESSFULLY_MESSAGE'), STATUS_CODE_SUCCESS, $data);
            }else{
                Log::info('User details Unauthorised');
                return errorResponse(trans('api-message.INVALID_MOBILE'),STATUS_CODE_NOT_FOUND);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Check Username
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/01/2022
    */

    /**
        * @OA\Post(
        * path="/api/v1/check-username",
        * operationId="check-username",
        * tags={"check-username"},
        * summary="check-username",
        * description="check-username",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"user_name"},
        *               @OA\Property(property="user_name", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Data does not exist.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Data does not exist.",
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
    public function checkUsername(Request $request)
    {
        try {
            $username = $request->user_name;
            $userName = User::where('user_name', $username)->get()->toArray();
            if (!empty($userName)) {
                Log::info('This Username already exists');
                return successResponseWithoutData(trans('api-message.USERNAME_ALREADY_EXISTS'),STATUS_CODE_SUCCESS);
            } else {
                return successResponseWithoutData(trans('api-message.DATA_DOES_NOT_EXIST'),STATUS_CODE_SUCCESS);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

/**
        * @OA\Post(
        * path="/api/v1/refresh-token",
        * operationId="refresh-token",
        * tags={"refresh-token"},
        * summary="Refresh Token",
        * description="Refresh Token",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"refresh_token","user_id"},
        *               @OA\Property(property="refresh_token", type="text"),
        *               @OA\Property(property="user_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Refresh token successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Refresh token successfully.",
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
    public function userRefreshToken(Request $request)
        {
            try {
                $user = User::where('id', $request->user_id)->with('consumer')->first();
                //$token = $user->createToken('SixPac')->accessToken;
                
                $tokenResult= $user->createToken(ACCESS_TOKEN);
                $token  = $tokenResult->token;
                $token->expires_at 	= setTokenExpiryTime();
                $token->refresh_at 	= setTokenRefreshTime();
                $tokenData = $tokenResult->accessToken;
                $data = [
                    'token' => $tokenData,
                ];
                return successResponse(trans('api-message.REFRESH_TOKEN_SUCCESSFULLY'), UNAUTHORIZED_TOKEN, $data);
            } catch (\Exception $e) {
                DB::rollBack();
                // Log error message
                Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                    '<Message>' => $e->getMessage(),
                ]));
                return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
            }
        }

}
