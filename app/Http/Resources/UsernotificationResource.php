<?php

namespace App\Http\Resources;

use App\Models\PostComment;
use App\Models\PostGallery;
use App\Models\Squad;
use App\Models\SquadMembers;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UsernotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $user_data = User::where('id',$this->sender_id)->first();
        if($this->notification_type == "post-add"){
            $message = "Post add";
            $squadRequestId = '';
            $postId = $this->post_id;
            $squad_name = "";
        }else if($this->notification_type == "post-like"){
            $message = "liked your post";
            $squadRequestId = '';
            $postId = $this->post_id;
            $squad_name = "";
        }else if($this->notification_type == "post-comment"){
            // $post_data = PostComment::where('id',$this->post_comments_id)->first();
            // $message = $post_data['comment'];
            $message = "commented on your post.";
            $squadRequestId = '';
            $postId = $this->post_id;
            $squad_name = "";
        }else if($this->notification_type == "squad-request"){
            $squadMember = SquadMembers::where('id',$this->post_id)->first();
            $squad_name = "";
            if(!empty($squadMember)){
                $squadData = Squad::where('id',$squadMember->squad_id)->first('squad_name');
                $squad_name = !empty($squadData->squad_name) ? $squadData->squad_name : "";
            }
            
            $message = $user_data->user_name." sent you request for join ".$squad_name."?";
            $squadRequestId = $this->post_id;
            $postId = '';
        }else if($this->notification_type == "recommended-request"){
            $squad_name = "";
            $squadData = Squad::where('id',$this->post_id)->first('squad_name');
            if(!empty($squadData)){
                $squad_name = !empty($squadData->squad_name) ? $squadData->squad_name : "";
            }
            
            $message = $user_data->user_name." sent you request for join ".$squad_name;
            $squadRequestId = $this->post_id;
            $postId = '';
        }else if($this->notification_type == "approved-squad-request"){
            $message = $user_data->user_name . 'Your squad request is approved';
            $squadRequestId = $this->post_id;
            $postId = '';
            $squad_name = "";
        }else if($this->notification_type == "reject-squad-request"){
            $message = $user_data->user_name . 'Your squad request is rejected';
            $squadRequestId = $this->post_id;
            $postId = '';
            $squad_name = "";
        } else{
            $message = "";
            $squadRequestId = "";
            $postId = "";
            $squad_name = "";
        }

        
       
        
        
        $gallery = array();
        if($this->notification_type == "post-like" || $this->notification_type == "post-comment" || $this->notification_type == "post-add") {
            $gallery = PostGalleryResource::collection(PostGallery::where('post_id', $this->post_id)->get());
        }
        $arr = [];
        $arr['id'] = $this->id;
        $arr['postId'] = $postId;
        $arr['squadName'] = $squad_name;
        $arr['squadRequestId'] = $squadRequestId;
        $arr['senderUserId'] = $this->sender_id;
        $arr['senderUserDetails'] = $user_data->user_name;
        $arr['senderUserImage'] = !empty($user_data->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$user_data->avtar) : asset('admin/images/faces/pic-1.png');
        $arr['message'] = $message;
        $arr['gallery'] = $gallery;
        $arr['updatePostAt'] = $this->updated_at;
        $arr['notificationType'] = $this->notification_type;
        $arr['status'] = $this->status;

       return $arr;
    }
}
