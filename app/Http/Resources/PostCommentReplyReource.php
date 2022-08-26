<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class PostCommentReplyReource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $PostOriginalImagePath = Config::get('constant.POST_ORIGINAL_PHOTO_UPLOAD_PATH');
        return [
            'id' => $this->id,
            'postId' => $this->post_id,
            'user' => new GetUsersDetailsResource($this->user),
            'parentId' => $this->parent_id,
            'comment' => $this->comment,
            'commentsImage' => !empty($this->comments_image) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($PostOriginalImagePath.$this->comments_image) : '',
            'commentsGif' => !empty($this->comments_gif) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($PostOriginalImagePath.$this->comments_gif) : '',
            'createdAt' => (string) $this->created_at,
            'updatedAt' => (string) $this->updated_at,
            'usersTag' => GetUsersDetailsResource::collection($this->commentTagTousers),
            'replies' => PostCommentReplyReource::collection($this->replies),
        ];
    }
}
