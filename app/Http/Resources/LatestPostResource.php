<?php

namespace App\Http\Resources;

use App\Models\FlagComment;
use App\Models\PostComment;
use App\Models\PostLike;
use Illuminate\Http\Resources\Json\JsonResource;

class LatestPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'users' => new GetUsersDetailsResource($this->user),
            'postCreater' => new GetUsersDetailsResource($this->postCreater),
            //'title' => $this->post_title,
            'slug' => $this->post_slug,
            'desc' => $this->post_content,
            'galleries' => PostGalleryResource::collection($this->galleries),
            'createdAt' => (string) $this->created_at,
            'postLikeCount' => PostLike::where('post_id',$this->id)->count(),
            'postCommentCount' => PostComment::where('post_id',$this->id)->count(),
            'galleriesCount' => $this->galleries_count,
            'isLike' => PostLike::isLike($this->id),
            //'comments' => PostCommentResource::collection($this->most_like_comments),
            'comments' => FeedPostCommentResource::collection($this->most_like_comments),
            //'comments' => $this->most_like_comments,
            'usersTag' => GetUsersDetailsResource::collection($this->postTagTousers),
            'reportProblemStatus' => FlagComment::postFlag($this->id),
            'shareStatus' => $this->share_status,
        ];
    }
}
