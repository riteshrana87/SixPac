<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConsumerDetailResource;
use App\Http\Resources\LatestPostResource;
use App\Http\Resources\SquadDataResource;
use App\Http\Resources\SquadDetailResource;
use App\Http\Resources\SquadResource;
use App\Http\Resources\UserPostResource;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\PostGallery;
use App\Models\SharePostWithSquad;
use App\Models\Squad;
use App\Models\SquadMembers;
use App\Models\User;
use App\Models\UserFollower;
use App\Models\UserPost;
use App\Services\ImageUpload;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SquadController extends Controller
{
    public function __construct()
    {
        $this->originalImagePath = Config::get('constant.SQUAD_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->thumbImagePath = Config::get('constant.SQUAD_THUMB_PHOTO_UPLOAD_PATH');
        $this->thumbImageHeight = Config::get('constant.SQUAD_THUMB_PHOTO_HEIGHT');
        $this->thumbImageWidth = Config::get('constant.SQUAD_THUMB_PHOTO_WIDTH');


        $this->squadBannerOriginalImagePath = Config::get('constant.SQUAD_BANNER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->squadBannerThumbImagePath = Config::get('constant.SQUAD_BANNER_THUMB_PHOTO_UPLOAD_PATH');
        $this->squadBannerThumbImageHeight = Config::get('constant.SQUAD_BANNER_THUMB_PHOTO_HEIGHT');
        $this->squadBannerThumbImageWidth = Config::get('constant.SQUAD_BANNER_THUMB_PHOTO_WIDTH');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $user_id = Auth::user()->id;
            $squadData = Squad::with(['members' => function ($q)  {
                $q->select('id','squad_id','member_id','leader_member_flag');
            },'members.user' => function ($q) {
                $q->select('id', 'name','user_name','avtar');
            }])
            ->where(array('created_by' => $user_id))
            ->get();
            $squadResult = SquadDataResource::collection($squadData);

            $data = array(
                'squadData' => $squadResult,
            );
            
            return successResponse(trans('api-message.SQUAD_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
        //dd($request->all());
        Log::info('Create user squad data');
        try {
                $squadData = new Squad();
                // Upload Squad Photo
            if (!empty($request->file('squad_profile_pic')) && $request->file('squad_profile_pic')->isValid()) {
                $params = [
                        'originalPath' => $this->originalImagePath,
                        'thumbPath' => $this->thumbImagePath,
                        'thumbHeight' => $this->thumbImageHeight,
                        'thumbWidth' => $this->thumbImageWidth,
                        'previousImage' => "",
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('squad_profile_pic'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.SQUAD_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $squadData->squad_profile_pic = $userPhoto['imageName'];
            }


            if (!empty($request->file('bannerPic')) && $request->file('bannerPic')->isValid()) {
                $paramsBanner = [
                        'originalPath' => $this->squadBannerOriginalImagePath,
                        'thumbPath' => $this->squadBannerThumbImagePath,
                        'thumbHeight' => $this->squadBannerThumbImageHeight,
                        'thumbWidth' => $this->squadBannerThumbImageWidth,
                        'previousImage' => "",
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('bannerPic'), $paramsBanner);
                if ($userPhoto === false) {
                    DB::rollback();
                    return errorResponse(trans('log-messages.SQUAD_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                }

                $squadData->banner_pic = $userPhoto['imageName'];
            }

                $squadData->squad_name = !empty($request->squad_name) ? $request->squad_name : NULL;
                $squadData->status = !empty($request->status) ? $request->status : '1';
                $squadData->notes = !empty($request->notes) ? $request->notes : NULL;
                $squadData->is_public = !empty($request->isPublic) ? $request->isPublic : '0';
                $squadData->created_by = Auth::user()->id;
                $squadData->updated_by = Auth::user()->id;
                $squadData->save();
            Log::info('Add squad detail successfully');

            // Start store squad member data
                if(!empty($request->squadMembers)){
                    $members = explode(",",$request->squadMembers);
                    foreach($members as $member){
                        $squadMember = new SquadMembers();
                        $squadMember->squad_id = $squadData->id;
                        $squadMember->member_id = $member;
                        $squadMember->leader_member_flag = 0;
                        $squadMember->status = 2;
                        $squadMember->save();
                    }
                }
                // End store squad member data

                // Start store squad leader data
                if(!empty($request->squadleaderId)){
                    $leadMembers = explode(",",$request->squadleaderId);
                    foreach($leadMembers as $leader){
                        $squadLeader = new SquadMembers();
                        $squadLeader->squad_id = $squadData->id;
                        $squadLeader->member_id = $leader;
                        $squadLeader->leader_member_flag = 1;
                        $squadLeader->status = 2;
                        $squadLeader->save();
                    }
                }
                // End store squad leader data

            $messge = trans('api-message.ADD_SQUAD_DETIAL_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Create squad details :: message :'.$message);
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
    public function update(Request $request)
    {
        //dd($request->id);
        try {
                $id = $request->id;
                if (!empty($request->file('squad_profile_pic')) && $request->file('squad_profile_pic')->isValid()) {
                    $params = [
                                'originalPath' => $this->originalImagePath,
                                'thumbPath' => $this->thumbImagePath,
                                'thumbHeight' => $this->thumbImageHeight,
                                'thumbWidth' => $this->thumbImageWidth,
                                'previousImage' => "",
                            ];

                    $userPhoto = ImageUpload::uploadWithThumbImage($request->file('squad_profile_pic'), $params);
                    if ($userPhoto === false) {
                        DB::rollback();
                        return errorResponse(trans('log-messages.SQUAD_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    }
    
                    $squadData['squad_profile_pic'] = $userPhoto['imageName'];
                }

                if (!empty($request->file('bannerPic')) && $request->file('bannerPic')->isValid()) {
                    $paramsBanner = [
                            'originalPath' => $this->squadBannerOriginalImagePath,
                            'thumbPath' => $this->squadBannerThumbImagePath,
                            'thumbHeight' => $this->squadBannerThumbImageHeight,
                            'thumbWidth' => $this->squadBannerThumbImageWidth,
                            'previousImage' => "",
                        ];
    
                    $userPhoto = ImageUpload::uploadWithThumbImage($request->file('bannerPic'), $paramsBanner);
                    if ($userPhoto === false) {
                        DB::rollback();
                        return errorResponse(trans('log-messages.SQUAD_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    }
    
                    $squadData['banner_pic'] = $userPhoto['imageName'];
                }

                $squadData['squad_name'] = !empty($request->squad_name) ? $request->squad_name : NULL;
                $squadData['notes'] = !empty($request->notes) ? $request->notes : NULL;
                $squadData['status'] = !empty($request->status) ? $request->status : '1';
                $squadData['is_public'] = !empty($request->isPublic) ? $request->isPublic : '0';
                $squadData['updated_by'] = Auth::user()->id;
                Squad::where('id', $id)->update($squadData);
                SquadMembers::where('squad_id',$id)->delete();

                // Start store squad member data
                if(!empty($request->squadMembers)){
                    $members = explode(",",$request->squadMembers);
                    foreach($members as $member){
                        $squadMember = new SquadMembers();
                        $squadMember->squad_id = $id;
                        $squadMember->member_id = $member;
                        $squadMember->leader_member_flag = 0;
                        $squadMember->status = 2;
                        $squadMember->save();
                    }
                }
                // End store squad member data

                // Start store squad leader data
                if(!empty($request->squadleaderId)){
                    $leadMembers = explode(",",$request->squadleaderId);
                    foreach($leadMembers as $leader){
                        $squadLeader = new SquadMembers();
                        $squadLeader->squad_id = $id;
                        $squadLeader->member_id = $leader;
                        $squadLeader->leader_member_flag = 1;
                        $squadLeader->status = 2;
                        $squadLeader->save();
                    }
                }
                // End store squad leader data

                $result = Squad::where(array('id' => $id))->first();
                $userSquadData = new SquadDataResource($result);

                $data = array(
                    'squadData' => $userSquadData,
                );
                Log::info('Edit squad detail successfully');
                return successResponse(trans('api-message.SQUAD_DETAILS_UPDATED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
           $message = $e->getMessage();
            Log::info('Edit squad details :: message :'.$message);
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
            Log::info('Delete User squad data API started !!');
            $squadData = Squad::find($id);
            if (empty($squadData)) {
                Log::warning('Food detail not found with id:' . $id);
                return errorResponse(trans('api-message.SQUAD_DETAILS_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);    
            }
            $squadData->delete();
            
            SquadMembers::where('squad_id',$id)->delete();


            Log::info('Delete Squad detail with id:' . $id);
            return successResponseWithoutData(trans('api-message.DELETE_SQUAD_DETIAL_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            Log::error('Unable to delete Squad due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_DELETE_SQUAD_DETAILS'),STATUS_CODE_ERROR);
        }
    }

     /*
        @Author : Ritesh Rana
        @Desc   : Get Food Data specified resource from storage.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 15/03/2022
    */

    public function getSquadData(Request $request)
    {
        try{
            $id = $request->id;
            $squadData = Squad::with(['members' => function ($q)  {
                $q->select('id','squad_id','member_id','leader_member_flag','status')->where('status',2);
            },'members.user' => function ($q) {
                $q->select('id', 'name','user_name','avtar');
            }])
            ->where(array('id' => $id))
            ->get();
            $squadResult = SquadDataResource::collection($squadData);

            $data = array(
                'squadData' => $squadResult,
            );
            
            return successResponse(trans('api-message.SQUAD_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : get list of squad all post data
        @Input  : User id
        @Output : \Illuminate\Http\Response
        @Date   : 27/04/2022
    */
    public function getSquadAllPost(Request $request){
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
            $squad_id = $request->squad_id;

            /*Get squad member list*/
            $membersId = SquadMembers::where('squad_id',$squad_id)->pluck('member_id')->toArray();
            $user_id = (int)Auth::user()->id;
            $user_post = Arr::prepend($membersId,$user_id);
            
            
            $posts_count = UserPost::withCount('galleries')
                ->where('squad_id',$squad_id)
                ->whereIn('user_id', $user_post)
                ->Where('status', 1)
                ->Where('is_public', 1)
                ->orderBy('created_at', 'DESC')
                ->get();
            $numrows = count($posts_count);
            /* Get squad post list */
            $posts = UserPost::withCount('galleries')
                ->where('squad_id',$squad_id)
                ->whereIn('user_id', $user_post)
                ->Where('status', 1)
                ->Where('is_public', 1)
                ->orderBy('created_at', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get(); 
                
            $userposts = LatestPostResource::collection($posts);

            $nextPage = LengthPager::makeLengthAware($userposts, $numrows, $perpage, []);
            Log::info('get list of user all post data');

            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'user_post' => $userposts
            );
            Log::info('create Squads Post list');
            return successResponse(trans('api-message.POST_LIST_FETCHED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch(\Exception $e){
            // Log Message
            Log::error('Get Squads All Post due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

     /*
        @Author : Ritesh Rana
        @Desc   : Get All User Squad details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 05/05/2022
    */
    public function getAllSquad(Request $request)
    {
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
            Log::info('Start get has tag data');
            $user_id = Auth::user()->id;


            $userSquadIds = SquadMembers::where('member_id', $user_id)
                        ->groupBy('squad_id') 
                        ->pluck('squad_id')
                        ->toArray();
                        
            /* start count squad list*/
            $squadCount = Squad::with(['members' => function ($q)  {
                $q->select('id','squad_id','member_id','leader_member_flag','status')->where('status',2);
            },'members.user' => function ($q) {
                $q->select('id', 'name','user_name','avtar');
            }]);
            // $squadData = $squadCount->where(function ($query) use($search_keyword) {
            //     if($search_keyword){ 
            //         $query->where('squad_name', 'like', '%' . $search_keyword . '%');
            //     }
            // })
            $squadData = $squadCount->where(array('created_by' => $user_id))
            ->orWhereIn('id',$userSquadIds)
            ->where(array('status' => 1))
            ->get();
            $numrows = count($squadData);
            
            /* End count has squad list*/
            
                $squadData = Squad::with(['members' => function ($q)  {
                    $q->select('id','squad_id','member_id','leader_member_flag','status')->where('status',2);
                },'members.user' => function ($q) {
                    $q->select('id', 'name','user_name','avtar');
                }]);
                // $squadList = $squadData->where(function ($query) use($search_keyword) {
                //     if($search_keyword){ 
                //         $query->where('squad_name', 'like', '%' . $search_keyword . '%');
                //     }
                // })
                $squadList = $squadData->where(array('created_by' => $user_id))
                ->orWhereIn('id',$userSquadIds)
                ->where(array('status' => 1))
                ->orderBy('squad_name', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();

            $squadResult = SquadResource::collection($squadList);
                
            /* End Get squad list*/    

            

            $nextPage = LengthPager::makeLengthAware($squadResult, $numrows, $perpage, []);

            if (!empty($squadResult)) {
                Log::info('Get Squad list Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'squadData' => $squadResult
                );
                return successResponse(trans('api-message.SQUAD_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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
        @Desc   : Get All Squad List details.
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 17/05/2022
    */
    public function getSquadList(Request $request)
    {
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
            Log::info('Start get has tag data');
            /* start count squad list*/
            $squadCount = Squad::with(['members' => function ($q)  {
                $q->select('id','squad_id','member_id','leader_member_flag','status')->where('status',2);
            },'members.user' => function ($q) {
                $q->select('id', 'name','user_name','avtar');
            }]);
            $squadData = $squadCount->where(function ($query) use($search_keyword) {
                if($search_keyword){ 
                    $query->where('squad_name', 'like', '%' . $search_keyword . '%');
                }
            })
            ->where(array('status' => 1))
            ->get();
            $numrows = count($squadData);
            
            /* End count has squad list*/
            
                $squadData = Squad::with(['members' => function ($q)  {
                    $q->select('id','squad_id','member_id','leader_member_flag','status')->where('status',2);
                },'members.user' => function ($q) {
                    $q->select('id', 'name','user_name','avtar');
                }]);
                $squadList = $squadData->where(function ($query) use($search_keyword) {
                    if($search_keyword){ 
                        $query->where('squad_name', 'like', '%' . $search_keyword . '%');
                    }
                })
                ->where(array('status' => 1))
                ->orderBy('squad_name', 'ASC')
                ->skip($offset)
                ->take($perpage)
                ->get();
                
             $squadResult = SquadDataResource::collection($squadList);
            
            /* End Get squad list*/    

            

            $nextPage = LengthPager::makeLengthAware($squadResult, $numrows, $perpage, []);

            if (!empty($squadResult)) {
                Log::info('Get Squad list Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'squadData' => $squadResult
                );
                return successResponse(trans('api-message.SQUAD_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);
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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     /**
        * @OA\Post(
        * path="/api/v1/send-request-to-squad",
        * operationId="Send request to squad",
        * tags={"send request to squad"},
        * summary="send request to squad",
        * description="send request to squad",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"squadId","memberId"},
        *               @OA\Property(property="squadId", type="text"),
        *               @OA\Property(property="memberId", type="text"),
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=201,
        *          description="Add request successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Add request successfully",
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
    public function sendRequestToSquad(Request $request)
    {
        try {

            Log::info('Like Comments API started !!');
            $squad = Squad::find($request->squadId);
            if (empty($squad)) {
                Log::warning('Sqquad not found with id:' . $request->squadId);
                return errorResponse(trans('api-message.SQUAD_DETAILS_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }

            $leader_member = SquadMembers::where('squad_id',$request->squadId)->where('leader_member_flag',1)->first('member_id');
            
            if(!empty($leader_member)){
                $squadMember = new SquadMembers();
                $squadMember->squad_id = $request->squadId;
                $squadMember->member_id = $request->memberId;
                $squadMember->leader_member_flag = 0;
                if($squad->is_public == 1){
                    $squadMember->status = 2;
                }else{
                    $squadMember->status = 1;
                }
                $squadMember->save();

                /*start Store data in notification table*/
            if($squad->is_public == 0){
                $notificationRecode = array(
                    'sender_id' => $request->memberId,
                    'receiver_id' => $leader_member->member_id,
                    'type' => 'squad-request',
                    'post_id' => $squadMember->id,
                    'status' => 0
                );
                Helper::userNotification($notificationRecode);    
            }
            /*End Store data in notification table*/
            }
            
            $userDetail = User::where('id',$request->memberId)->first('user_name');
            

            /*Start send push notification*/
            $deviceToken = DeviceToken::getAllUsersDeviceToken($leader_member->member_id);
                if(!empty($deviceToken)){
                    if($squad->is_public == 0){
                        $notificationData = array(
                            'deviceToken' => $deviceToken,
                            //'title' => Auth::user()->user_name,
                            'message' => $userDetail->user_name . 'Send request to join '.$squad->squad_name.' Squad',
                            'postId' => $leader_member->member_id,
                            // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                        );
                        Helper::sendPushNotification($notificationData);
                    }
                }
            /*End send push notification*/

            Log::info('Add request successfully');
            return successResponseWithoutData(trans('api-message.ADD_REQUEST_SUCCESSFULLY'), STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approvedSquadRequest(Request $request)
    {
        try {
            $id = $request->SquadRequestId;
            $notificationId = $request->notificationId;

            $squadRequest = SquadMembers::find($id);
            if (empty($squadRequest)) {
                Log::info('Request not found with id:' . $id);
                return errorResponse(trans('api-message.REQUEST_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }
            /*Start Get Squad leader Id*/
            $leaderMemberData = SquadMembers::where('squad_id',$squadRequest->squad_id)->where('leader_member_flag',1)->first('member_id');
            /*End Get Squad leader Id*/

            /*Start check requested user is add in squad leader follower list*/
                $checkUserFollower = UserFollower::where('user_id',$leaderMemberData->member_id)->where('follower_id',$squadRequest->member_id)->where('status',2)->first();
             /*End check requested user is add in squad leader follower list*/

            /*start if squad leader follower list in not add requested user add follower list*/
            if(empty($checkUserFollower)){
                $follower = new UserFollower();
                $follower->user_id = $leaderMemberData->member_id;
                $follower->follower_id = $squadRequest->member_id;
                $follower->status = 2;
                $follower->save();
            }
            /*End if squad leader follower list in not add requested user add follower list*/

            $squadRequest->status = 2;
            $squadRequest->update();

            /* Start Delete notification data */
                $notificationData = Notification::find($notificationId);
                if(!empty($notificationData)){
                    $notificationData->delete();
                }
            /* End Delete notification data */

            /*start Store data in notification table*/
                $notificationRecode = array(
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $squadRequest->member_id,
                    'type' => 'approved-squad-request',
                    'post_id' => $squadRequest->id,
                    'status' => 0
                );
                Helper::userNotification($notificationRecode);
            /*End Store data in notification table*/
             /*Start send push notification*/
             $userDetail = User::where('id',$squadRequest->member_id)->first('user_name');
             $deviceToken = DeviceToken::getAllUsersDeviceToken($squadRequest->member_id);
             if(!empty($deviceToken)){
                 $notificationData = array(
                     'deviceToken' => $deviceToken,
                     //'title' => Auth::user()->user_name,
                     'message' => $userDetail->user_name . 'Your squad request is approved',
                     'postId' => $squadRequest->member_id,
                     // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                 );
                 Helper::sendPushNotification($notificationData);
             }
            /*End send push notification*/

            Log::info('Approved your squad request.');
            $messge = trans('api-message.APPROVED_YOUR_SQUAD_REQUEST_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            Log::info('Approved your squad request Error :: message :' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function rejectSquadRequest(Request $request)
    {
        try {
            $id = $request->SquadRequestId;
            $notificationId = $request->notificationId;
            /* Check squad request */
            $squadRequest = SquadMembers::find($id);
            if (empty($squadRequest)) {
                Log::info('Request not found with id:' . $id);
                return errorResponse(trans('api-message.REQUEST_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }
            //$squadRequest->status = 0;
            //$squadRequest->update();
            /* Start Delete notification data */
            $notificationData = Notification::find($notificationId);
            $notificationData->delete();
            /* End Delete notification data */

            /*start Store data in notification table*/
                $notificationRecode = array(
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $squadRequest->member_id,
                    'type' => 'reject-squad-request',
                    'post_id' => $squadRequest->id,
                    'status' => 0
                );
                Helper::userNotification($notificationRecode);
            /*End Store data in notification table*/

            /*Start send push notification*/
                $userDetail = User::where('id',$squadRequest->member_id)->first('user_name');
                $deviceToken = DeviceToken::getAllUsersDeviceToken($squadRequest->member_id);
                if(!empty($deviceToken)){
                    $notificationData = array(
                        'deviceToken' => $deviceToken,
                        //'title' => Auth::user()->user_name,
                        'message' => $userDetail->user_name . 'join Your squad request is rejected',
                        'postId' => $squadRequest->member_id,
                        // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                    );
                    Helper::sendPushNotification($notificationData);
                }
           /*End send push notification*/
           $squadRequest->delete();
            Log::info('reject your squad request.');
            $messge = trans('api-message.REJECT_YOUR_SQUAD_REQUEST_SUCCESSFULLY');
            return successResponseWithoutData($messge, STATUS_CODE_SUCCESS);

        } catch (\Exception $e) {
            Log::info('reject your squad request Error :: message :' . $e->getMessage());
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    
    public function seeAllRecommendedSquad(Request $request){
        try{
            if(!empty($request->page)) {
                $page = $request->page;
            } else {
                $page = "1";
            }

            if (!empty($request->perpage)) {
                $perpage = $request->perpage;
            } else {
                $perpage = Config::get('constant.LIST_PER_PAGE');
            }
            $offset = ($page - 1) * $perpage;

            $user = auth()->user();
            $userId = $user->id;
            //Follwer friend list
            $UsersFollwerId = User::userFollowing();

            /*Get user squad list*/
            $userSquadIds = SquadMembers::where('member_id', $userId)
                        ->groupBy('squad_id') 
                        ->pluck('squad_id')
                        ->toArray();
            
            /*Get follower squad list*/
            $squadIds = SquadMembers::whereIn('member_id', $UsersFollwerId)
                        ->whereNotIn('squad_id', $userSquadIds)
                        ->groupBy('squad_id') 
                        ->pluck('squad_id')
                        ->toArray();

            /*Get squad count*/
            $squadCount = Squad::withCount('members')->whereIn('id', $squadIds)
                        ->where(array('status' => 1))
                        ->get();
            $numrows = count($squadCount);
            
            /*Get squad list with pagination*/
            $squadList = Squad::withCount('members')->whereIn('id', $squadIds)
                        ->where(array('status' => 1))
                        ->skip($offset)
                        ->take($perpage)
                        ->get();
            
            $squadResult = SquadDetailResource::collection($squadList);

            $nextPage = LengthPager::makeLengthAware($squadResult, $numrows, $perpage, []);
            Log::info('Recommended squad list fetched successfully');

            // Get response success
            if (!empty($squadResult)) {
                Log::info('Get Squad list Details');
                $data = array(
                    'totalCount' => $numrows,
                    'perpage' => $perpage,
                    'nextPage' => $nextPage,
                    'squadData' => $squadResult
                );
            return successResponse(trans('api-message.RECOMMENDED_SQUAD_DETAILS_FETCH_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

            } else {
                return errorResponse(trans('api-message.RECORD_NOT_FOUND'),STATUS_CODE_NOT_FOUND);
            }

        } catch(\Exception $e){
            // Log Message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return response()->json([
                'status' => 500,
                'message' => trans('api-message.DEFAULT_ERROR_MESSAGE'),
            ]);
        }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendRecommendedSquadToUser(Request $request)
    {
        try {

            Log::info('Recommended to squad API started !!');
            $squad = Squad::find($request->squadId);

            $squad_id = $request->squadId;
            $member_id = $request->memberId;
            
            if (empty($squad)) {
                Log::warning('Sqquad not found with id:' . $request->squadId);
                return errorResponse(trans('api-message.SQUAD_DETAILS_NOT_FOUND'),STATUS_CODE_INSUFFICIENT_DATA);
            }

           // $check_member = SquadMembers::where('squad_id',$request->squadId)->where('member_id',$member_id)->first();
            $check_member = Notification::where('post_id',$request->squadId)
            ->where('receiver_id',$member_id)
            ->where('notification_type','recommended-request')
            ->first();
            
            if(empty($check_member)){
                
                $squad_id = $request->squadId;
                $member_id = $request->memberId;
                
                /*start Store data in notification table*/
            
                $notificationRecode = array(
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $member_id,
                    'type' => 'recommended-request',
                    'post_id' => $squad_id,
                    'status' => 0
                );
                Helper::userNotification($notificationRecode);    
            
            /*End Store data in notification table*/
            
            /*Start send push notification*/
            $deviceToken = DeviceToken::getAllUsersDeviceToken($request->memberId);
                if(!empty($deviceToken)){
                    if($squad->is_public == 0){
                        $notificationData = array(
                            'deviceToken' => $deviceToken,
                            //'title' => Auth::user()->user_name,
                            'message' => Auth::user()->user_name . 'invite you to join this '.$squad->squad_name,
                            'postId' => $squad_id,
                            // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                        );
                        Helper::sendPushNotification($notificationData);
                    }
                }
            /*End send push notification*/
                
                Log::info('Add request successfully');
                return successResponseWithoutData(trans('api-message.ADD_REQUEST_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
            }else{
                Log::info('Already joined squad');
                return successResponseWithoutData(trans('api-message.ALREADY_JOINED_SQUAD'), STATUS_CODE_ERROR);
            }
        
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }



}
