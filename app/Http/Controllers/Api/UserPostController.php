<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\LatestPostResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\UserPostResource;
use App\Models\CommentsUpvoteAndDownvote;
use App\Models\CommentTagToUsers;
use App\Models\DeviceToken;
use App\Models\FlagComment;
use App\Models\HashTags;
use App\Models\Notification;
use App\Models\PostComment;
use App\Models\PostGallery;
use App\Models\PostLike;
use App\Models\PostTagTousers;
use App\Models\User;
use App\Models\UserPost;
use App\Models\UsersTagsToPost;
use App\Services\ImageUpload;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserPostController extends Controller
{
    public function __construct()
    {
        $this->postOriginalImagePath = Config::get('constant.POST_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->postThumbImagePath = Config::get('constant.POST_THUMB_PHOTO_UPLOAD_PATH');
        $this->postThumbImageHeight = Config::get('constant.POST_THUMB_PHOTO_HEIGHT');
        $this->postThumbImageWidth = Config::get('constant.POST_THUMB_PHOTO_WIDTH');
    }
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
        * path="/api/v1/userpost",
        * operationId="userpost",
        * tags={"Add User Post"},
        * summary="Add User Post",
        * description="Add User Post (file and videoThumbImage upload multipul if not working in swager please use postman collection)",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"post_text","public_publish","status"},
        *               @OA\Property(property="post_text", type="text"),
        *               @OA\Property(property="public_publish", type="text"),
        *               @OA\Property(property="status", type="text"),
        *               @OA\Property(property="file", type="file"),
        *               @OA\Property(property="hashTag", type="text"),
        *               @OA\Property(property="usersTag", type="text"),
        *               @OA\Property(property="squadId", type="text"),
        *               @OA\Property(property="videoThumbImage",type="file"),        
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add Post successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add Post successfully",
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
        Log::info('Create Post');
        // $validator = Validator::make($request->all(), [
        //     //'title' => 'required',
        //     'post_text' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     Log::info('Post Validator Fails');
        //     return response()->json(['message' => $validator->errors(),'status' => 422], 422);
        // }

        try {
            $post_slug = Str::random(20);
            $post = new UserPost();
            $post->user_id = Auth::user()->id;
            // $post->created_by = Auth::user()->id;
            // $post->post_title = $request->title;
            $post->post_slug = $post_slug;
            $post->post_content = $request->post_text;
            $post->status = $request->status;
            $post->is_public = $request->public_publish;
            $post->squad_id = isset($request->squadId) & !empty($request->squadId) ? $request->squadId : NULL;
            $post->save();

            /*Start Check and store has tag data*/
            $usersTag = $request->usersTag;
            if(!empty($usersTag)){
                $uTagData = array_unique(explode(",",$request->usersTag));
                foreach($uTagData as $tagId){
                        $userTagData = new PostTagTousers();
                        $userTagData->post_id = $post->id;
                        $userTagData->user_id = $tagId;
                        $userTagData->save();
                }
            }
            /*End Check has tag data*/

            /*Start Check and store has tag data*/


            $hashTags = $request->hashTag;
            if(!empty($hashTags)){
                $tagData = explode(",",$request->hashTag);
                foreach($tagData as $hashTag){
                    $checkHasTag = HashTags::where('status',1)->where('hash_tag_name',$hashTag)->first();
                    if(empty($checkHasTag)){
                        $hasTagData = new HashTags();
                        $hasTagData->hash_tag_name = $hashTag;
                        $hasTagData->save();
                    }
                }
            }
            /*End Check has tag data*/
            if (!empty($request->file('file'))) {
                $imageParams = [
                    'originalPath' => $this->postOriginalImagePath,
                    'thumbPath' => $this->postThumbImagePath,
                    'thumbHeight' => $this->postThumbImageHeight,
                    'thumbWidth' => $this->postThumbImageWidth,
                    'previousImage' => $request->hidden_photo,
                ];
                foreach ($request->file('file') as $file) {
                    $postgallery = new PostGallery();
                    $postgallery->post_id = $post->id;

                    // Upload User Photo
                    if (!empty($file) && $file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileMediaOrder = $file->getClientOriginalName();
                        $fileMediaOrderName = pathinfo($fileMediaOrder, PATHINFO_FILENAME);
                        if (in_array($extension, Config::get('constant.IMAGE_EXTENSION')))
                        {

                            $postPhoto = ImageUpload::uploadImage($file, $imageParams, $post->id);
                            $postgallery['thumb_name'] = $postPhoto['thumbName'];
                        }
                        else if (in_array($extension, Config::get('constant.VIDEO_EXTENSION')))
                        {
                            $params = [
                                        'originalPath' => $this->postOriginalImagePath,
                                        'thumbPath' => $this->postThumbImagePath,
                                        'thumbHeight' => $this->postThumbImageHeight,
                                        'thumbWidth' => $this->postThumbImageWidth,
                                        'previousVideo' =>  "",
                                     ];
                            $postPhoto = ImageUpload::uploadVideo($file, $params, $post->id);

                            $fileData = $file->getClientOriginalName();
                            $fileName = pathinfo($fileData, PATHINFO_FILENAME);

                            if (!empty($request->file('videoThumbImage'))) {
                                foreach ($request->file('videoThumbImage') as $thumbfile) {
                                    $ThumbImageExt = $thumbfile->getClientOriginalExtension();

                                    $thumbData = $thumbfile->getClientOriginalName();
                                    $thumbName = pathinfo($thumbData, PATHINFO_FILENAME);
                                    if (in_array($ThumbImageExt, Config::get('constant.IMAGE_EXTENSION')))
                                        {
                                            if($thumbName == $fileName){
                                                $videoThumbImage = ImageUpload::uploadImage($thumbfile, $imageParams, $post->id);
                                                if(!empty($videoThumbImage)){
                                                    $postgallery['thumb_name'] = $videoThumbImage['thumbName'];
                                                }
                                            }
                                        }
                                }
                            }
                        }

                        if ($postPhoto === false) {
                            DB::rollback();
                            $response = [];
                            $response['message'] = "error";
                            $response['status'] = 500;
                            Log::info(trans('log-messages.POST_IMAGE_UPLOAD_ERROR_MESSAGE'));
                            return response()->json($response, 500);
                        }
                        $postgallery['media_order'] = $fileMediaOrderName;
                        $postgallery['file_name'] = $postPhoto['imageName'];
                        $postgallery['file_type'] = $postPhoto['fileType'];
                   }
                   //dd($postgallery);
                   $postgallery->save();
                   Log::info('Store image in gallery');
               }
            }

            $user = Auth::user();
            /*Start Store notification data*/
            $notificationData = [];
            $following = $user->checkSendNotificationToAddPost()->pluck('follower_id')->toArray();
            foreach ($following as $followinUser) {
                $notificationRecode = array(
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $followinUser,
                    'notification_type' => 'post-add',
                    'post_id' => $post->id,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                array_push($notificationData,$notificationRecode);
            }
            Notification::insert($notificationData);
            /*End Store notification data*/

            /*Start send push notification*/
                $deviceTokens = DeviceToken::whereIn('user_id', $following)->pluck('device_token')->toArray();
                if(!empty($deviceTokens)){
                    $notificationData = array(
                        'deviceToken' => $deviceTokens,
                        //'title' => Auth::user()->username,
                        'message' => Auth::user()->user_name . '  Add New post',
                        'postId' => $post->id,
                        // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                    );
                    Helper::sendPushNotification($notificationData);
                }
            /*End send push notification*/

            Log::info('Add Post successfully');
            return successResponseWithoutData(trans('api-message.ADD_POST_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::info('Edit Food details :: message :'.$message);
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
        * @OA\Get(
        * path="/api/v1/get-post-detail/{id}",
        * operationId="get-post-detail",
        * tags={"Get Post Detail"},
        * summary="Get Post Detail",
        * description="Get Post Detail",
         *      @OA\Parameter(
     *          name="id",
     *          description="Project id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
        *      @OA\Response(
        *          response=201,
        *          description="Post deleted successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post deleted successfully !!",
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
    public function show($id)
    {
        try {
            $userpost = UserPost::withCount('galleries')->find($id);
            $postdata = new UserPostResource($userpost);

            $data = array(
                'post_data' => $postdata,
            );
            Log::info('create Post list');
            return successResponse(trans('api-message.GET_POST_DETAIL_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to like Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
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
        * path="/api/v1/edit-post",
        * operationId="edit-post",
        * tags={"Edit User Post"},
        * summary="Edit User Post",
        * description="Edit User Post (file and videoThumbImage upload multipul if not working in swager please use postman collection)",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"post_text","public_publish","status"},
        *               @OA\Property(property="post_text", type="text"),
        *               @OA\Property(property="public_publish", type="text"),
        *               @OA\Property(property="status", type="text"),
        *               @OA\Property(property="file", type="file"),
        *               @OA\Property(property="hashTag", type="text"),
        *               @OA\Property(property="usersTag", type="text"),
        *               @OA\Property(property="squadId", type="text"),
        *               @OA\Property(property="videoThumbImage",type="file"),        
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Post updated successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post updated successfully !!",
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
        Log::info('Create Post');
        // $validator = Validator::make($request->all(), [
        //     //'title' => 'required',
        //     'post_text' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     Log::info('Post Validator Fails');
        //     return errorResponse($validator->errors(),VALIDATOR_CODE_ERROR);
        // }

        try {
            $post_id = $request->post_id;
            $userpost = UserPost::find($post_id);
            if (empty($userpost)) {
                Log::info('Post not found with id:' . $post_id);
                return errorResponse(trans('api-message.POST_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }
            $post_slug = Str::random(20);
            $userpost->user_id = Auth::user()->id;
            //$userpost->post_title = $request->title;
            $userpost->post_slug = $post_slug;
            $userpost->post_content = $request->post_text;
            $userpost->status = $request->status;
            $userpost->is_public = $request->public_publish;
            $userpost->update();

            /*Start Check and store has tag data*/
            $usersTag = $request->usersTag;
            if(!empty($usersTag)){
                $uTagData = array_unique(explode(",",$request->usersTag));
                PostTagTousers::where('post_id',$post_id)->delete();
                foreach($uTagData as $tagId){
                        $userTagData = new PostTagTousers();
                        $userTagData->post_id = $post_id;
                        $userTagData->user_id = $tagId;
                        $userTagData->save();
                }
            }
            /*End Check has tag data*/


            /*Start Check and store has tag data*/
            $hashTags = $request->hashTag;
            if(!empty($hashTags)){
                $tagData = explode(",",$request->hashTag);
                foreach($tagData as $hashTag){
                    $checkHasTag = HashTags::where('status',1)->where('hash_tag_name',$hashTag)->first();
                    if(empty($checkHasTag)){
                        $hasTagData = new HashTags();
                        $hasTagData->hash_tag_name = $hashTag;
                        $hasTagData->save();
                    }
                }
            }
            /*End Check has tag data*/
            PostGallery::where('post_id',$post_id)->delete();
            if (!empty($request->file('file'))) {
                $imageParams = [
                    'originalPath' => $this->postOriginalImagePath,
                    'thumbPath' => $this->postThumbImagePath,
                    'thumbHeight' => $this->postThumbImageHeight,
                    'thumbWidth' => $this->postThumbImageWidth,
                    'previousImage' => $request->hidden_photo,
                ];

                Log::info('Post file delete with id:' . $post_id);
                foreach ($request->file('file') as $file) {
                    $postgallery = new PostGallery;
                    $postgallery->post_id = $post_id;
                    // Upload User Photo
                    if (!empty($file) && $file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileMediaOrder = $file->getClientOriginalName();
                        $fileMediaOrderName = pathinfo($fileMediaOrder, PATHINFO_FILENAME);
                        if (in_array($extension, Config::get('constant.IMAGE_EXTENSION')))
                        {
                            //$postPhoto = ImageUpload::uploadWithThumbImage($file, $params, $post_id);
                            $postPhoto = ImageUpload::uploadImage($file, $imageParams, $post_id);
                            $postgallery['thumb_name'] = $postPhoto['thumbName'];

                        }
                        else if (in_array($extension, Config::get('constant.VIDEO_EXTENSION')))
                        {
                            $params = [
                                        'originalPath' => $this->postOriginalImagePath,
                                        'thumbPath' => $this->postThumbImagePath,
                                        'thumbHeight' => $this->postThumbImageHeight,
                                        'thumbWidth' => $this->postThumbImageWidth,
                                        'previousVideo' =>  "",
                                        ];
                            $postPhoto = ImageUpload::uploadVideo($file, $params, $post_id);

                            $fileData = $file->getClientOriginalName();
                            $fileName = pathinfo($fileData, PATHINFO_FILENAME);
                            /* End Upload thumb image  */
                            if (!empty($request->file('videoThumbImage'))) {
                                foreach ($request->file('videoThumbImage') as $thumbfile) {
                                    $ThumbImageExt = $thumbfile->getClientOriginalExtension();

                                    $thumbData = $thumbfile->getClientOriginalName();
                                    $thumbName = pathinfo($thumbData, PATHINFO_FILENAME);
                                    if (in_array($ThumbImageExt, Config::get('constant.IMAGE_EXTENSION')))
                                        {
                                            if($thumbName == $fileName){
                                                $videoThumbImage = ImageUpload::uploadImage($thumbfile, $imageParams, $post_id);
                                                if(!empty($videoThumbImage)){
                                                    $postgallery['thumb_name'] = $videoThumbImage['thumbName'];
                                                }
                                            }
                                        }
                                }
                            }
                            /* End Upload thumb image  */
                        }

                        if ($postPhoto === false) {
                            DB::rollback();
                            $response = [];
                            $response['message'] = "error";
                            $response['status'] = 500;
                            Log::info(trans('log-messages.POST_IMAGE_UPLOAD_ERROR_MESSAGE'));
                            return response()->json($response, 500);
                        }
                        $postgallery['media_order'] = $fileMediaOrderName;
                        $postgallery['file_name'] = $postPhoto['imageName'];
                        $postgallery['file_type'] = $postPhoto['fileType'];
                    }

                    $postgallery->save();
                }
            }
            Log::info('Post updated.');
            return successResponseWithoutData(trans('api-message.POST_UPDATED_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::info('Update Post Error :: message :' . $e->getMessage());
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
        * path="/api/v1/delete-post/{id}",
        * operationId="delete-post",
        * tags={"Delete post"},
        * summary="delete-post",
        * description="delete-post",
         *      @OA\Parameter(
     *          name="id",
     *          description="Project id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
        *      @OA\Response(
        *          response=201,
        *          description="Post deleted successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post deleted successfully !!",
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
            Log::info('Delete Post API started !!');
            $userpost = UserPost::find($id);
            if (empty($userpost)) {
                Log::warning('Post not found with id:' . $id);
                return errorResponse(trans('api-message.POST_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }
            $getPostdata = PostGallery::where('post_id', $id)->get();
            foreach ($getPostdata as $postdata) {
                if (Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists($this->postOriginalImagePath.$postdata['file_name'])) {
                    Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->delete($this->postOriginalImagePath.$postdata['file_name']);
                }
                if (Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists($this->postThumbImagePath.$postdata['file_name'])) {
                    Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->delete($this->postThumbImagePath.$postdata['file_name']);
                }
            }
            PostGallery::where('post_id', $id)->delete();
            $userpost->delete();

            Log::info('Delete Post with id:' . $id);
            return successResponseWithoutData(trans('api-message.POST_DELETE_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Unable to delete Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_DELETE_POST'),STATUS_CODE_ERROR);
        }
    }
    /*
        @Author : Ritesh Rana
        @Desc   : User Like post
        @Input  : post id
        @Output : \Illuminate\Http\Response
        @Date   : 25/02/2022
    */

    /**
        * @OA\Get(
        * path="/api/v1/like-post/{id}",
        * operationId="like-post",
        * tags={"like post"},
        * summary="like-post",
        * description="like-post",
         *      @OA\Parameter(
     *          name="id",
     *          description="Project id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
        *      @OA\Response(
        *          response=201,
        *          description="Post Liked successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post Liked successfully !!",
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
    public function liekPost($post_id)
    {
        try {
            Log::info('Like Post API started !!');
            $userpost = UserPost::find($post_id);

            if (empty($userpost)) {
                Log::warning('Post not found with id:' . $post_id);
                return errorResponse(trans('api-message.POST_NOT_FOUND'), STATUS_CODE_INSUFFICIENT_DATA);
            }

            $postLike = PostLike::where('post_id', $post_id)->where('user_id', Auth::user()->id)->first();
            $notificationRecode = array(
                'sender_id' => Auth::user()->id,
                'receiver_id' => $userpost->user_id,
                'type' => 'post-like',
                'post_id' => $userpost->id,
                'status' => 0
            );

            $deviceToken = DeviceToken::getAllUsersDeviceToken($userpost->user_id);
            if ($postLike) {
                if($userpost->user_id != Auth::user()->id){
                    Helper::userNotification($notificationRecode);
                    /*Start Send push notification */
                    if(!empty($deviceToken)){
                            $notificationData = array(
                                'deviceToken' => $deviceToken,
                                //'title' => Auth::user()->username,
                                'message' => Auth::user()->user_name . ' unliked your post',
                                'postId' => $post_id,
                                // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                            );
                            Helper::sendPushNotification($notificationData);
                    }
                    /*End Send push notification */
                }


                $postLike->delete();
                Log::info('Already  Post is like by with user:' . Auth::user()->id);
                $messge = trans('api-message.POST_LIKE_REMOVED');
                return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
            } else {
                DB::beginTransaction();
                $postLike = new PostLike();
                $postLike->user_id = Auth::user()->id;
                $postLike->post_id = $post_id;
                $post_data = $postLike->save();
                DB::commit();
                if($userpost->user_id != Auth::user()->id){
                Helper::userNotification($notificationRecode);
                /*Start Send push notification */
                if(!empty($deviceToken)){
                        $notificationData = array(
                            'deviceToken' => $deviceToken,
                            //'title' => Auth::user()->username,
                            'message' => Auth::user()->user_name . ' liked your post',
                            'postId' => $post_id,
                            // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                        );
                        Helper::sendPushNotification($notificationData);
                }
                /*End Send push notification */
                     }
                Log::info('Post '.$post_id.'Liked by ' . Auth::user()->id);
                $messge = trans('api-message.POST_LIKED_SUCCESSFULLY');
                return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to like Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_LIKE_POST'),STATUS_CODE_ERROR);
        }
    }


    /*
        @Author : Ritesh Rana
        @Desc   : get list of user all post data
        @Input  : User id
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

     /**
        * @OA\Post(
        * path="/api/v1/users-all-post",
        * operationId="users-all-post",
        * tags={"Get User Post List"},
        * summary="Get User Post List",
        * description="Get User Post List",
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
        *          description="Post list fetched successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post list fetched successfully",
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
    public function getUsersAllPost(Request $request){
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
            $user_id = $request->user_id;

            $posts_count = UserPost::withCount('galleries')
                ->Where('user_id', $user_id)
                ->whereNull('squad_id')
                ->Where('status', 1)
                ->Where('is_public', 1)
                ->orderBy('created_at', 'DESC')
                ->get();
            $numrows = count($posts_count);

            $posts = UserPost::withCount('galleries')
                ->Where('user_id', $user_id)
                ->whereNull('squad_id')
                ->Where('status', 1)
                ->Where('is_public', 1)
                ->orderBy('created_at', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();

           $userposts = UserPostResource::collection($posts);

            $nextPage = LengthPager::makeLengthAware($userposts, $numrows, $perpage, []);
            Log::info('get list of user all post data');

            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'user_post' => $userposts
            );
            Log::info('create Users Post list');
            return successResponse(trans('api-message.POST_LIST_FETCHED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch(\Exception $e){
            // Log Message
            Log::error('Get Users All Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
 /*
        @Author : Ritesh Rana
        @Desc   : add Post Comment
        @Input  : User id,post id, comment
        @Output : \Illuminate\Http\Response
        @Date   : 01/03/2022
    */
     /**
        * @OA\Post(
        * path="/api/v1/add-post-comment",
        * operationId="add-post-comment",
        * tags={"Add post comment"},
        * summary="add-post-comment",
        * description="add-post-comment",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"user_id","post_id","comment"},
        *               @OA\Property(property="user_id", type="text"),
        *               @OA\Property(property="post_id", type="text"),
        *               @OA\Property(property="comment", type="text"),
        *               @OA\Property(property="parent_id", type="text"),
        *               @OA\Property(property="comments_video", type="text"),
        *               @OA\Property(property="hashTag", type="text"),
        *               @OA\Property(property="usersTag", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Post Comment successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post Comment successfully !!",
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
    public function addPostComment(Request $request)
    {
        Log::info('Create Post Comment');
        // $validator = Validator::make($request->all(), [
        //     'user_id' => 'required',
        //     'post_id' => 'required',
        //     'comment' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     Log::info('Post Comment Validator Fails');
        //     return response()->json(['message' => $validator->errors(),'status' => 422], 422);
        // }

        try {
            $userPost = UserPost::find($request->get('post_id'));

            if (empty($userPost)) {
                Log::warning('Post not found with id:' . $request->get('post_id'));
                return errorResponse(trans('api-message.POST_NOT_FOUND'), STATUS_CODE_INSUFFICIENT_DATA);
            }

            $notificationRecode = array(
                'sender_id' => Auth::user()->id,
                'receiver_id' => $userPost->user_id,
                'type' => 'post-comment',
                'post_id' => $userPost->id,
                'status' => 0
            );

            if ($request->parent_id) {
                DB::beginTransaction();
                $reply = new PostComment();
                $reply->comment = $request->get('comment');
                $reply->user_id = $request->get('user_id');
                $reply->post_id = $request->get('post_id');

                if (!empty($request->file('comments_image')) && $request->file('comments_image')->isValid()) {
                    $params = [
                        'originalPath' => $this->postOriginalImagePath,
                        'thumbPath' => $this->postThumbImagePath,
                        'thumbHeight' => $this->postThumbImageHeight,
                        'thumbWidth' => $this->postThumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];
                    //print_r($params);exit();
                    $commentPhoto = ImageUpload::uploadWithThumbImage($request->file('comments_image'), $params);
                    if ($commentPhoto === false) {
                        DB::rollback();
                        return errorResponse(trans('log-messages.POST_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    }
                    $reply->comments_image = $commentPhoto['imageName'];
                    //print_r($comment);exit();
                }

                if (!empty($request->file('comments_video')) && $request->file('comments_video')->isValid()) {
                    $params = [
                        'originalPath' => $this->postOriginalImagePath,
                        'thumbPath' => $this->postThumbImagePath,
                        'thumbHeight' => $this->postThumbImageHeight,
                        'thumbWidth' => $this->postThumbImageWidth,
                        'previousVideo' => "",
                    ];
                    $postPhoto = ImageUpload::uploadWithThumbImage($request->file('comments_video'), $params);
                    if ($postPhoto === false) {
                        DB::rollback();
                        return errorResponse(trans('log-messages.POST_VIDEO_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    }
                    //$reply->comments_video = $postPhoto['comments_video'];
                    $reply->comments_video = $postPhoto['videoName'];
                }

                 $reply->user()->associate($request->user());
                $reply->parent_id = $request->get('parent_id');

                $post = UserPost::find($request->get('post_id'));
                $com_data = $post->comments()->save($reply);


                 /*Start Check and store has tag data*/
                 $usersTag = $request->usersTag;
                 if(!empty($usersTag)){
                     $uTagData = array_unique(explode(",",$request->usersTag));
                     CommentTagToUsers::where('comment_id',$com_data->id)->delete();
                     foreach($uTagData as $tagId){
                             $userTagData = new CommentTagToUsers();
                             $userTagData->comment_id = $com_data->id;
                             $userTagData->user_id = $tagId;
                             $userTagData->save();
                    }
                }
                 /*End Check has tag data*/


                 /*Start Check and store has tag data*/
                 $hashTags = $request->hashTag;
                 if(!empty($hashTags)){
                     $tagData = explode(",",$request->hashTag);
                     foreach($tagData as $hashTag){
                         $checkHasTag = HashTags::where('status',1)->where('hash_tag_name',$hashTag)->first();
                         if(empty($checkHasTag)){
                             $hasTagData = new HashTags();
                             $hasTagData->hash_tag_name = $hashTag;
                             $hasTagData->save();
                         }
                     }
                 }
                /*End Check and store has tag data*/

                $postComment = PostComment::find($reply->parent_id);
                $postComments = new PostCommentResource($postComment);
                if($userPost->user_id != $request->user_id){
                    Helper::userNotification($notificationRecode);
                /*Start send push notification*/
                    $deviceToken = DeviceToken::getAllUsersDeviceToken($userPost->user_id);
                    if(!empty($deviceToken)){
                        $notificationData = array(
                            'deviceToken' => $deviceToken,
                            //'title' => Auth::user()->username,
                            'message' => Auth::user()->user_name . '  Commented: "'. $request->get('comment').'"',
                            'postId' => $request->get('post_id'),
                            // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                        );
                        Helper::sendPushNotification($notificationData);
                    }
                }
                /*End send push notification*/
                DB::commit();
                Log::info('comment reply successfully ' . $reply->id);

                $data = array(
                    'comments' => $postComments
                );

                return successResponse(trans('api-message.POST_REPLY_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
            } else {
                DB::beginTransaction();
                $commentVal = new PostComment;
                $commentVal->comment = $request->comment;
                $commentVal->user_id = $request->user_id;

                if (!empty($request->file('comments_image')) && $request->file('comments_image')->isValid()) {
                    $params = [
                        'originalPath' => $this->postOriginalImagePath,
                        'thumbPath' => $this->postThumbImagePath,
                        'thumbHeight' => $this->postThumbImageHeight,
                        'thumbWidth' => $this->postThumbImageWidth,
                        'previousImage' => $request->hidden_photo,
                    ];
                    //print_r($params);exit();
                    $commentPhoto = ImageUpload::uploadWithThumbImage($request->file('comments_image'), $params);
                    if ($commentPhoto === false) {
                        DB::rollback();
                        return errorResponse(trans('log-messages.POST_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    }
                    $commentVal->comments_image = $commentPhoto['imageName'];
                    //print_r($comment);exit();
                }


                if (!empty($request->file('comments_video')) && $request->file('comments_video')->isValid()) {
                    $params = [
                        'originalPath' => $this->postOriginalImagePath,
                        'thumbPath' => $this->postThumbImagePath,
                        'thumbHeight' => $this->postThumbImageHeight,
                        'thumbWidth' => $this->postThumbImageWidth,
                        'previousVideo' =>  "",
                    ];
                    $postPhoto = ImageUpload::uploadWithThumbImage($request->file('comments_video'), $params);
                    if ($postPhoto === false) {
                        DB::rollback();
                        return errorResponse(trans('log-messages.POST_VIDEO_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    }
                    //$commentVal->comments_video = $postPhoto['comments_video'];
                    $commentVal->comments_video = $postPhoto['videoName'];

               }
                $commentVal->user()->associate($request->user());
                $commentVal->post_id = $request->post_id;
                $post = UserPost::find($request->post_id);
                $com_data = $post->comments()->save($commentVal);


                /*Start Check and store has tag data*/
                $usersTag = $request->usersTag;
                if(!empty($usersTag)){
                    $uTagData = array_unique(explode(",",$request->usersTag));
                    CommentTagToUsers::where('comment_id',$com_data->id)->delete();
                    foreach($uTagData as $tagId){
                            $userTagData = new CommentTagToUsers();
                            $userTagData->comment_id = $com_data->id;
                            $userTagData->user_id = $tagId;
                            $userTagData->save();
                    }
                }
                /*End Check has tag data*/


                /*Start Check and store has tag data*/
                $hashTags = $request->hashTag;
                if(!empty($hashTags)){
                    $tagData = explode(",",$request->hashTag);
                    foreach($tagData as $hashTag){
                        $checkHasTag = HashTags::where('status',1)->where('hash_tag_name',$hashTag)->first();
                        if(empty($checkHasTag)){
                            $hasTagData = new HashTags();
                            $hasTagData->hash_tag_name = $hashTag;
                            $hasTagData->save();
                        }
                    }
                }
            /*End Check and store has tag data*/

                $postComment = PostComment::find($com_data->id);
                $postComments = new PostCommentResource($postComment);
                /*Start send push notification*/
                if($userPost->user_id != $request->user_id){
                    Helper::userNotification($notificationRecode,$com_data->id);
                    $deviceToken = DeviceToken::getAllUsersDeviceToken($userPost->user_id);
                    if(!empty($deviceToken)){
                        $notificationData = array(
                            'deviceToken' => $deviceToken,
                            //'title' => Auth::user()->username,
                            'message' => Auth::user()->user_name . '  Commented: "'. $request->get('comment').'"',
                            'postId' => $request->get('post_id'),
                            // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                        );
                        Helper::sendPushNotification($notificationData);
                    }
                }

                /*End send push notification*/
               DB::commit();
               Log::info('comment added successfully ' . $commentVal->id);

               // Get response success
                $data = array(
                    'comments' => $postComments
                );
                return successResponse(trans('api-message.POST_COMMENT_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to comment Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_COMMENT_POST'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : see All Latest Update Post Post data
        @Input  : User id,post id, comment
        @Output : \Illuminate\Http\Response
        @Date   : 15/03/2022
    */

    /**
        * @OA\Post(
        * path="/api/v1/feeds",
        * operationId="feeds",
        * tags={"feeds"},
        * summary="feeds",
        * description="feeds",
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
        *          description="Latest Update Post list fetched successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Latest Update Post list fetched successfully",
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
    public function seeAllLatestUpdatePost(Request $request){
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
            $superAdmin = User::where('role',1)->where('status',1)->first('id');
            $superId = $superAdmin->id;

            $offset = ($page - 1) * $perpage;
            $UsersfriendId = User::userFollowing();
            $user_id = (int)$request->user_id;
            $user_post = Arr::prepend($UsersfriendId,$user_id);
            array_push($user_post,$superId);

            $latest_count = UserPost::withCount('galleries')->where('status', 1)
                ->where('is_public',1)
                ->whereNull('squad_id')
                ->whereIn('user_id', $user_post)
                ->orderBy('created_at', 'DESC')
                ->get();

                $numrows = count($latest_count);

            $latest_update = UserPost::withCount('galleries')->where('status', 1)
                ->where('is_public',1)
                ->whereNull('squad_id')
                ->whereIn('user_id', $user_post)
                ->orderBy('created_at', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();


        //     $latest_update = UserPost::with(['comments' => function ($q)  {
        //         $q->select('id','post_id','parent_id','user_id','commentable_type','commentable_id','comment','comments_image','comments_video','created_at');
        //     },'comments.commentLike' => function ($q) {
        //         $q->select('id', 'comments_id');
        //     }])->withCount('galleries')
        //    // ->select('user_posts.*', DB::raw('COUNT(comments_upvote_and_downvote.id) AS popularity'))
        //         ->where('status', 1)
        //         ->where('is_public',1)
        //         ->whereNull('squad_id')
        //         ->whereIn('user_id', $user_post)
        //         ->orderBy('created_at', 'DESC')
        //         ->skip($offset)
        //         ->take($perpage)
        //         ->get();



                //return $latest_update;

            $latestUpdatePost = LatestPostResource::collection($latest_update);
            // array merge
            $feeds = $latestUpdatePost->collection->toArray();
            //shuffle($feeds);

            $nextPage = LengthPager::makeLengthAware($feeds, $numrows, $perpage, []);

            Log::info('Friends Are Liking list fetched successfully');

            // Get current page form url e.x. &page=1
            //$currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Create a new Laravel collection from the array data
            $itemCollection = collect($feeds);

            // Slice the collection to get the items to display in current page
           // $currentPageItems = $itemCollection->slice(($currentPage * $perpage) - $perpage, $perpage)->all();
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'user_post' => $itemCollection
            );
            Log::info('Get response success');
            return successResponse(trans('api-message.GET_LATEST_UPDATE_POST_LIST'), STATUS_CODE_SUCCESS, $data);

            // Get response success
            // return response()->json([
            //     'status' => 200,
            //     'message' => trans('api-message.GET_LATEST_UPDATE_POST_LIST'),
            //     'totalCount' => $numrows,
            //     'perpage' => $perpage,
            //     'nextPage' => $nextPage,
            //     'data' => $currentPageItems,
            // ], 200);

        } catch(\Exception $e){
            // Log Message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Comments Upvote
        @Input  : User id,comment id, vote
        @Output : \Illuminate\Http\Response
        @Date   : 28/04/2022
    */
     /**
        * @OA\Post(
        * path="/api/v1/comments-upvote-downvote",
        * operationId="comments-upvote-downvote",
        * tags={"Comments upvote downvote"},
        * summary="comments upvote downvote",
        * description="Comments upvote downvote",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"comment_id","status"},
        *               @OA\Property(property="comment_id", type="text"),
        *               @OA\Property(property="status", type="text"),
        *               @OA\Property(property="perpage", type="text"),
            
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Comment voteing successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Comment voteing successfully !!",
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
    public function  commentsUpvoteDownvote(Request $request)
    {
        try {
            Log::info('Like Comments API started !!');
            $userpost = PostComment::find($request->comment_id);
            if (empty($userpost)) {
                Log::warning('Comments not found with id:' . $request->comment_id);
                return errorResponse(trans('api-message.COMMENTS_DETAILS_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }

            if($request->status == 1){
                $type = "comments-upvote";
            }else{
                $type = "comments-downvote";
            }

            $commentUpVote = CommentsUpvoteAndDownvote::where('comments_id', $request->comment_id)->where('user_id', Auth::user()->id)->first();
            $notificationRecode = array(
                'sender_id' => Auth::user()->id,
                'receiver_id' => $userpost->user_id,
                'type' => $type,
                'type_id' => $userpost->id,
                'status' => 0
            );

            if ($commentUpVote) {
                Helper::userNotification($notificationRecode);
                /*Start send push notification*/
                $deviceToken = DeviceToken::getAllUsersDeviceToken($userpost->user_id);
                if(!empty($deviceToken)){
                    $notificationData = array(
                        'deviceToken' => $deviceToken,
                        //'title' => Auth::user()->username,
                        'message' => Auth::user()->user_name . ' Comments downvote',
                        'postId' => $userpost->post_id,
                        // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                    );
                    Helper::sendPushNotification($notificationData);
                }
                /*End send push notification*/

                $vote = CommentsUpvoteAndDownvote::find($commentUpVote->id);
                $vote->status = $request->status;
                $vote->update();
                Log::info('Already Comment is upvote by with user:' . Auth::user()->id);
                $messge = trans('api-message.COMMENT_VOTEING_SUCCESSFULLY');
                return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
            } else {
                DB::beginTransaction();
                $commentsVote = new CommentsUpvoteAndDownvote();
                $commentsVote->user_id = Auth::user()->id;
                $commentsVote->comments_id = $request->comment_id;
                $commentsVote->status = $request->status;
                $comments_data = $commentsVote->save();
                DB::commit();

                Helper::userNotification($notificationRecode);

                /*Start send push notification*/
                $deviceToken = DeviceToken::getAllUsersDeviceToken($userpost->user_id);
                if(!empty($deviceToken)){
                    $notificationData = array(
                        'deviceToken' => $deviceToken,
                        //'title' => Auth::user()->username,
                        'message' => Auth::user()->user_name . ' Comments upvote',
                        'postId' => $userpost->post_id,
                        // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                    );
                    Helper::sendPushNotification($notificationData);
                }
                /*End send push notification*/

                Log::info('Comment '. $request->comment_id.'Liked by ' . Auth::user()->id);
                return successResponseWithoutData(trans('api-message.COMMENT_VOTEING_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
            }



        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to voteing Comment due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_VOTEING_COMMENT'),STATUS_CODE_ERROR);
        }
    }


    /*
        @Author : Ritesh Rana
        @Desc   : get list post by search of hash tag name
        @Input  : search tag ,page,perpage
        @Output : \Illuminate\Http\Response
        @Date   : 24/05/2022
    */
     /**
        * @OA\Post(
        * path="/api/v1/get-post-by-hash-tag",
        * operationId="get-post-by-hash-tag",
        * tags={"Get post by hash tag"},
        * summary="Get post by hash tag",
        * description="Get post by hash tag",
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
        *          description="Post list fetched successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post list fetched successfully",
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
    public function getPostByHashTag(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'search_keyword' => 'required',
            ]);

            if ($validator->fails()) {
                Log::info('The search fields is required');
                return errorResponse(trans('api-message.SEARCH_REQUIRED'),VALIDATOR_CODE_ERROR);
            }

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
            $search_keyword = $request->search_keyword;

            //Start create query for count data
                $posts_count = UserPost::withCount('galleries')
                    ->where('post_content', 'like', '%' . $search_keyword . '%')
                    ->whereNull('squad_id')
                    ->Where('status', 1)
                    ->Where('is_public', 1)
                    ->orderBy('created_at', 'DESC')
                    ->get();
                $numrows = count($posts_count);
            //End create query for count data

            //Start create query for get data
                $posts = UserPost::withCount('galleries')
                    ->where('post_content', 'like', '%' . $search_keyword . '%')
                    ->whereNull('squad_id')
                    ->Where('status', 1)
                    ->Where('is_public', 1)
                    ->orderBy('created_at', 'DESC')
                    ->skip($offset)
                    ->take($perpage)
                    ->get();
            //End create query for get data
            $userposts = UserPostResource::collection($posts);

            $nextPage = LengthPager::makeLengthAware($userposts, $numrows, $perpage, []);
            Log::info('get list of user all post data');

            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'user_post' => $userposts
            );
            Log::info('create Users Post list');
            return successResponse(trans('api-message.POST_LIST_FETCHED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch(\Exception $e){
            // Log Message
            Log::error('Get Users All Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }



    /*
        @Author : Ritesh Rana
        @Desc   : add Post Comment
        @Input  : User id,post id, comment
        @Output : \Illuminate\Http\Response
        @Date   : 01/03/2022
    */
     /**
        * @OA\Post(
        * path="/api/v1/report-problem",
        * operationId="report-problem",
        * tags={"Report problem"},
        * summary="Report problem",
        * description="Report problem",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"userId","postId"},
        *               @OA\Property(property="userId", type="text"),
        *               @OA\Property(property="postId", type="text"),
        *               @OA\Property(property="commentId", type="text"),
            
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Post list fetched successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post list fetched successfully",
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
    public function reportProblem(Request $request)
    {
        Log::info('Create Post Comment');
        try {

            if(isset($request->commentId) && !empty($request->commentId)){
                $userComment = PostComment::find($request->commentId);
                if (empty($userComment)) {
                    Log::warning('Comment not found with id:' . $request->get('post_id'));
                    return errorResponse(trans('api-message.COMMENTS_DETAILS_NOT_FOUND'), STATUS_CODE_INSUFFICIENT_DATA);
                }
            }

            if(!empty($request->postId)){
                $userPost = UserPost::find($request->postId);
                if (empty($userPost)) {
                    Log::warning('Post not found with id:' . $request->get('post_id'));
                    return errorResponse(trans('api-message.POST_NOT_FOUND'), STATUS_CODE_INSUFFICIENT_DATA);
                }
            }

               // DB::beginTransaction();
                    $commentVal = new FlagComment;
                    $commentVal->comment_id = isset($request->commentId) ? $request->commentId : NULL;
                    $commentVal->user_id = $request->userId;
                    $commentVal->post_id = $request->postId;
                    $commentVal->save();

                    $countData = FlagComment::where('post_id',$request->postId)
                    ->whereNull('comment_id')
                    ->count();

                if($countData == 10){
                    $userPost->delete();
                }

              // DB::commit();
               Log::info('comment added successfully ' . $commentVal->id);

                return successResponseWithoutData(trans('api-message.YOUR_INCIDENT_REGISTERED_SUCCESSFULLY'), STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to comment Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_COMMENT_POST'),STATUS_CODE_ERROR);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Share Post With Squad.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 10/06/2022
    */
     /**
        * @OA\Post(
        * path="/api/v1/share-post-with-squad",
        * operationId="share-post-with-squad",
        * tags={"share-post-with-squad"},
        * summary="Share post with squad",
        * description="Share post with squad",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"squad_id","post_id"},
        *               @OA\Property(property="squad_id", type="text"),
        *               @OA\Property(property="post_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Post Share successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post Share successfully !!",
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
    public function sharePostWithSquad(Request $request)
    {
        try {
            Log::info('Share Post API started !!');
            $post_id = $request->post_id;
            $squad_id = $request->squad_id;
            $getPost = UserPost::with('galleries')->where('id',$post_id)->first();
            if ($getPost) {

                $post = new UserPost();
                $post->user_id = Auth::user()->id;
                $post->created_by = $getPost->created_by;
                $post->squad_id = $squad_id;
                $post->post_title = $getPost->post_title;
                $post->post_slug = $getPost->post_title;
                $post->post_content = $getPost->post_content;
                $post->notes = $getPost->notes;
                $post->status = $getPost->status;
                $post->share_status = 1;
                $post->is_public = $getPost->is_public;
                $post->save();

                if (!empty($getPost->galleries)) {
                    foreach ($getPost->galleries as $galleries) {
                        $postgallery = new PostGallery();
                        $postgallery->post_id = $post->id;
                        $postgallery->file_name = $galleries->file_name;
                        $postgallery->file_type = $galleries->file_type;
                        $postgallery->thumb_name = $galleries->thumb_name;
                        $postgallery->media_order = $galleries->media_order;
                        $postgallery->file_length = $galleries->file_length;
                        $postgallery->file_size = $galleries->file_size;
                        $postgallery->status = $galleries->status;
                        $postgallery->save();
                    }
                }

                Log::info('Post ' . $post_id . 'Share by ' . $squad_id);
                return successResponseWithoutData(trans('api-message.POST_SHARE_SUCCESSFULLY'), STATUS_CODE_SUCCESS);

            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to Share Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_SHARE_POST'),STATUS_CODE_NOT_FOUND);
        }
    }

    /*
        @Author : Ritesh Rana
        @Desc   : Share Post Squad Tom Feed.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 05/07/2022
    */
 /**
        * @OA\Post(
        * path="/api/v1/share-post-squad-to-feed",
        * operationId="share-post-squad-to-feed",
        * tags={"share-post-squad-to-feed"},
        * summary="Share post squad to feed",
        * description="Share post squad to feed",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"post_id"},
        *               @OA\Property(property="post_id", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Post Share successfully !!",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Post Share successfully !!",
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
    
    public function sharePostSquadToFeed(Request $request)
    {
        try {
            Log::info('Share Post API started !!');
            $post_id = $request->post_id;
            $getPost = UserPost::with('galleries')->where('id',$post_id)->first();
            if ($getPost) {
                $post = new UserPost();
                $post->user_id = Auth::user()->id;
                $post->created_by = $getPost->created_by;
                $post->post_title = $getPost->post_title;
                $post->post_slug = $getPost->post_title;
                $post->post_content = $getPost->post_content;
                $post->notes = $getPost->notes;
                $post->status = $getPost->status;
                $post->share_status = 1;
                $post->is_public = $getPost->is_public;
                $post->save();

                if (!empty($getPost->galleries)) {
                    foreach ($getPost->galleries as $galleries) {
                        $postgallery = new PostGallery();
                        $postgallery->post_id = $post->id;
                        $postgallery->file_name = $galleries->file_name;
                        $postgallery->file_type = $galleries->file_type;
                        $postgallery->thumb_name = $galleries->thumb_name;
                        $postgallery->media_order = $galleries->media_order;
                        $postgallery->file_length = $galleries->file_length;
                        $postgallery->file_size = $galleries->file_size;
                        $postgallery->status = $galleries->status;
                        $postgallery->save();
                    }
                }

                Log::info('Post ' . $post_id . 'Share by feed');
                return successResponseWithoutData(trans('api-message.POST_SHARE_SUCCESSFULLY'), STATUS_CODE_SUCCESS);

            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to Share Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_SHARE_POST'),STATUS_CODE_NOT_FOUND);
        }
    }

}