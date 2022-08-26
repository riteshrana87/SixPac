<?php

namespace App\Http\Resources;

use App\Models\CommentsUpvoteAndDownvote;
use App\Models\FlagComment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FeedPostCommentResource extends JsonResource
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
        $PostOriginalImagePath = Config::get('constant.POST_ORIGINAL_PHOTO_UPLOAD_PATH');
        
        return [
            'id' => $this->id,
            'postId' => $this->post_id,
            'userId' => $this->user_id,
            'commentBy' => !empty($this->usersInfo->name) ? $this->usersInfo->name: '',
            'profilePic' => !empty($this->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->user->avtar) : asset('admin/images/faces/pic-1.png'),
            'thumbProfilePic' => !empty($this->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->user->avtar) : asset('admin/images/faces/pic-1.png'),
            'parentId' => $this->parent_id,
            'comment' => $this->comment,
            'commentsImage' => !empty($this->comments_image) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($PostOriginalImagePath.$this->comments_image) : '',
            'commentsVideo' => !empty($this->comments_video) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($PostOriginalImagePath.$this->comments_video) : '',
            'comment_upvote_count' => $this->comment_like_count,
            'isUpVote' => CommentsUpvoteAndDownvote::isUpVote($this->id),
            'isDownVote' => CommentsUpvoteAndDownvote::isDownVote($this->id),
            'createdAt' => (string) $this->created_at,
            'updatedAt' => (string) $this->updated_at,
            'replies' => PostCommentReplyReource::collection($this->replies),
            'commentReportProblemStatus' => FlagComment::commentFlag($this->id),
        ];
    }
}
