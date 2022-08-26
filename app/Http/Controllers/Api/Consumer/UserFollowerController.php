<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\FollowerResource;
use App\Http\Resources\FollowingsResource;
use App\Http\Resources\UserRegisterResource;
use App\Models\DeviceToken;
use App\Models\User;
use App\Models\UserFollower;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserFollowerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        * path="/api/v1/user-follower",
        * operationId="user-follower",
        * tags={"User follower"},
        * summary="User follower",
        * description="User follower",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"follower_id"},
        *               @OA\Property(property="follower_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add follower successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add follower successfully",
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
        Log::info('Create follower');
        $validator = Validator::make($request->all(), [
            'follower_id' => 'required',
        ]);

        if ($validator->fails()) {
            Log::info('follower Validator Fails');
            return response()->json(['message' => $validator->errors(),'status' => 422], 422);
        }

        try {
            $follower = new UserFollower();
            $follower->user_id = Auth::user()->id;
            $follower->follower_id = $request->follower_id;
            $follower->status = 1;
            $follower->save();
            
            $user_following_id = $follower->id;
            /*start Store data in notification table*/
            $notificationRecode = array(
                'sender_id' => Auth::user()->id,
                'receiver_id' => $request->follower_id,
                'type' => 'user-follower',
                'post_id' => NULL,
                'status' => 0
            );
            Helper::userNotification($notificationRecode);
            /*End Store data in notification table*/
            /*Start send push notification*/
            $deviceToken = DeviceToken::getAllUsersDeviceToken($request->follower_id);
                if(!empty($deviceToken)){
                    $notificationData = array(
                        'deviceToken' => $deviceToken,
                        //'title' => Auth::user()->username,
                        'message' => Auth::user()->user_name . ' Started following you',
                        'postId' => $follower->id,
                        // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                    );
                    Helper::sendPushNotification($notificationData);
                }
            /*End send push notification*/

        Log::info('Add follower successfully');
        $data = array(
            'user_following_id' => $user_following_id,
        );
        return successResponse(trans('api-message.ADD_FOLLOWER_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
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
        * path="/api/v1/auth/approved",
        * operationId="approved",
        * tags={"User Follower approved"},
        * summary="User Follower approved",
        * description="User Follower approved",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"user_following_id"},
        *               @OA\Property(property="user_following_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Follower updated successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Follower updated successfully.",
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
            $id = $request->user_following_id;
            $follower = UserFollower::find($id);
            if (empty($follower)) {
                Log::info('Follower not found with id:' . $id);
                return errorResponse(trans('api-message.FOLLOWER_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }
            $follower->status = 2;
            $follower->update();

            // $notificationRecode = array(
            //     'sender_id' => Auth::user()->id,
            //     'receiver_id' => $follower->follower_id,
            //     'type' => 'user-follower',
            //     'post_id' => NULL,
            //     'status' => 0
            // );
            // Helper::userNotification($notificationRecode);

            Log::info('Follower updated.');
            $messge = trans('api-message.FOLLOWER_UPDATEd_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            Log::info('Update Follower Error :: message :' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     /**
        * @OA\Delete(
        * path="/api/v1/remove-followers/{id}",
        * operationId="remove-followers",
        * tags={"Remove followers"},
        * summary="Remove followers",
        * description="Remove followers",
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
        *          description="Follower deleted successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Follower deleted successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Follower not found"),
        *      @OA\Response(response=404, description="Whoops! Something went wrong. Please try again."),
        * )
        */
    public function removeFollowers($id)
    {
        try {
            Log::info('Deleted Follower API started !!');
            $user_following_id = UserFollower::followersGetId($id);
            $follower = UserFollower::find($user_following_id);
            //print_r($follower);exit();
            if (empty($follower)) {
                Log::warning('Follower not found with id:' . $id);
                return response()->json(['message' => 'Follower not found','status' => 400], 400);
            }
            $follower->status = 5;
            $follower->update();

            $notificationRecode = array(
                'sender_id' => Auth::user()->id,
                'receiver_id' => $id,
                'type' => 'user-follower',
                'post_id' => NULL,
                'status' => 0
            );
            Helper::userNotification($notificationRecode);

            Log::info('Deleted Follower with id:' . $id);
            $messge = trans('api-message.DELETED_FOLLOWER');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            Log::error('Unable to Deleted Follower due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_DELETED_FOLLOWER'),STATUS_CODE_ERROR);
        }
    }

/*
        @Author : Ritesh Rana
        @Desc   : Get User Followings Data.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 24/02/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/followings",
        * operationId="followings",
        * tags={"Followings"},
        * summary="Followings",
        * description="Followings",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"user_id","page","perpage"},
        *               @OA\Property(property="user_id", type="text"),
        *               @OA\Property(property="page", type="text"),
        *               @OA\Property(property="perpage", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="New Release following list fetched successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="New Release following list fetched successfully.",
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
    public function followings(Request $request){
        // dd(auth()->user());
         try{
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
             $findUser = User::where('id', $request->user_id)->first();
             //$userfollowingCount = auth()->user()->following()->get();
 
             $userfollowingCount = $findUser->following()->get();
             $numrows = count($userfollowingCount);
 
             $userfollowing = $findUser->following()
                 //->orderBy('created_at', 'DESC')
                 ->skip($offset)
                 ->take($perpage)
                 ->get();
             $followingdata = FollowingsResource::collection($userfollowing);
 
             $nextPage = LengthPager::makeLengthAware($followingdata, $numrows, $perpage, []);
             Log::info('create following list fetched successfully');
             // Get response success
             $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'following_users' => $followingdata
            );
            return successResponse(trans('api-message.NEW_RELEASE_FOLLOWING_LIST'), STATUS_CODE_SUCCESS, $data);
 
         } catch(\Exception $e){
             // Log Message
            Log::error('Get following list due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
         }
     }


    /*
        @Author : Ritesh Rana
        @Desc   : Get User followers Data.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */
    /**
        * @OA\Post(
        * path="/api/v1/followers",
        * operationId="followers",
        * tags={"followers"},
        * summary="User Follower List",
        * description="User Follower List",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"user_id","page","perpage"},
        *               @OA\Property(property="user_id", type="text"),
        *               @OA\Property(property="page", type="text"),
        *               @OA\Property(property="perpage", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="New Release Follower list fetched successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="New Release Follower list fetched successfully",
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
     public function followers(Request $request){
        try{
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
            $findUser = User::where('id', $request->user_id)->first();
            $userfollowersCount = $findUser->followers()->get();
            $numrows = count($userfollowersCount);

            $userfollowers = $findUser->followers()
                //->orderBy('created_at', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();
            
            $followersdata = FollowerResource::collection($userfollowers);

            $nextPage = LengthPager::makeLengthAware($followersdata, $numrows, $perpage, []);
            Log::info('create Follower list fetched successfully');
            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'followers_users' => $followersdata
            );
            return successResponse(trans('api-message.NEW_RELEASE_FOLLOWER_LIST'), STATUS_CODE_SUCCESS, $data);

        } catch(\Exception $e){
            // Log Message
            Log::error('Get Follower list due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

     /**
     * delete the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
        * @OA\Delete(
        * path="/api/v1/remove-followings/{id}",
        * operationId="remove-followings",
        * tags={"Remove followings"},
        * summary="Remove followings",
        * description="Remove followings",
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
        *          description="Deleted Following !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Deleted Following !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Following not found"),
        *      @OA\Response(response=404, description="Whoops! Something went wrong. Please try again."),
        * )
        */
    public function removefollowings($id)
    {
        try {
            Log::info('Deleted Following API started !!');
            $user_following_id = UserFollower::followingGetId($id);
            $follower = UserFollower::find($user_following_id);
            //print_r($follower);exit();
            if (empty($follower)) {
                Log::warning('Following not found with id:' . $id);
                return response()->json(['message' => 'Following not found','status' => 400], 400);
            }
            $follower->status = 5;
            $follower->update();

            $notificationRecode = array(
                'sender_id' => Auth::user()->id,
                'receiver_id' => $id,
                'type' => 'user-following',
                'post_id' => NULL,
                'status' => 0
            );
            Helper::userNotification($notificationRecode);

            Log::info('Deleted Following with id:' . $id);
            $messge = trans('api-message.DELETED_FOLLOWING');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            Log::error('Unable to Deleted Follower due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_DELETED_FOLLOWING'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : get User following And followers
        @Input  : int  $request
        @Output : \Illuminate\Http\Response
        @Date   : 05/05/2022
    */

    /**
        * @OA\Post(
        * path="/api/v1/following-and-followers-unique-user",
        * operationId="following-and-followers-unique-user",
        * tags={"Following And Followers Unique User"},
        * summary="Following And Followers Unique User",
        * description="Following And Followers Unique User",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"search_keyword","page","perpage"},
        *               @OA\Property(property="search_keyword", type="text"),
        *               @OA\Property(property="page", type="text"),
        *               @OA\Property(property="perpage", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Get user data successfully.",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Get user data successfully.",
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
    public function getUserfollowingAndfollowers(Request $request)
    {
        try {
            $search_keyword = $request->search_keyword;
            $id = Auth::user()->id;
            $user = User::where('id', $id)->first();  
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
            /*Get following and followers list*/
            $following = $user->following()->pluck('follower_id')->toArray();
            $follower = $user->followers()->pluck('user_id')->toArray();
            
            $arr = array_merge($following,$follower);
            $arrUnique = array_unique($arr);
            
            /* start count user list*/
            $usersArr = User::with('consumer')->where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('name', 'like', '%' . $search_keyword . '%')
					->orWhere('user_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $userCount = $usersArr->whereIn('id',$arrUnique)
                        ->whereNotIn('id', [$id])
                        ->where(array('otp_verified' => 1, 'status' => 1))->get();
            $numrows = count($userCount);
            /* End count user list*/
            
            /* start get user list*/
            $usersIdArr = User::with('consumer')->where(function ($query) use($search_keyword) {
				if($search_keyword){ 
					$query->where('name', 'like', '%' . $search_keyword . '%')
					->orWhere('user_name', 'like', '%' . $search_keyword . '%');
				}
			});
            $user = $usersIdArr->whereIn('id',$arrUnique)
                ->whereNotIn('id', [$id])
                ->where(array('otp_verified' => 1, 'status' => 1))
                ->orderBy('name', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();
            /* End Get user list*/    

            $users = UserRegisterResource::collection($user);

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
}
