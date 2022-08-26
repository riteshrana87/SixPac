<?php

namespace App\Http\Resources;

use App\Models\FlagComment;
use App\Models\PostComment;
use App\Models\PostLike;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //dd($this->galleries);
        return [
            'id' => $this->id,
            'users' => new GetUsersDetailsResource($this->user),
            //'title' => $this->post_title,
            'slug' => $this->post_slug,
            'desc' => $this->post_content,
            'galleries' => PostGalleryResource::collection($this->galleries),
            'createdAt' => (string) $this->created_at,
            // 'postLikeCount' => (int) $this->likes->count(),
            // 'postCommentCount' => (int) $this->comments->count(),
            'postLikeCount' => PostLike::where('post_id',$this->id)->count(),
            'postCommentCount' => PostComment::where('post_id',$this->id)->count(),
            'isLike' => PostLike::isLike($this->id),
            //'likes' => GetUsersDetailsResource::collection($this->likes()->orderByDesc('post_likes.created_at')->take(2)->get()),
            'galleriesCount' => $this->galleries_count,
            'comments' => PostCommentResource::collection($this->comments),
            'usersTag' => GetUsersDetailsResource::collection($this->postTagTousers),
            'isPublic' => $this->is_public,
            'status' => $this->status,
            'reportProblemStatus' => FlagComment::postFlag($this->id),
        ];
    }
}