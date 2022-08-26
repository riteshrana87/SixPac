<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ExcerciseVideoResource extends JsonResource
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
            'id' => ($this->id === null || $this->id == '') ? '' : $this->id,
            'duration' =>($this->lengh === null || $this->lengh == '') ? 0 : intval($this->lengh),
            'size' => ($this->size === null || $this->size == '') ? 0 : intval($this->size),
            'image'=>($this->thumb_path === null || $this->thumb_path == '') ? '' : (Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->exists(Config::get('constant.EXCERCISE_THUMB_PHOTO_UPLOAD_PATH') . $this->thumb_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.STORY_VIDEO_THUMB_UPLOAD_PATH') . $this->thumb_path) : (Storage::disk('s3')->exists(Config::get('constant.STORY_VIDEO_THUMB_UPLOAD_PATH') . $this->thumb_path) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.EXCERCISE_THUMB_PHOTO_UPLOAD_PATH') . $this->thumb_name) : '')),
            'chunkPointer' => ($this->chunkPointer === null || $this->chunkPointer == '') ? 0 : intval($this->chunkPointer),
            'uploadPercent' => ($this->progressPercent === null || $this->progressPercent == '') ? 0 : $this->progressPercent,
            'uploadComplete' => boolval($this->completed_at),
            'uploadPause' => boolval($this->resumed_at),
            $this->mergeWhen(isset($this->videoSignedUrl) && !empty($this->videoSignedUrl),[
                'videoUrl' => $this->videoSignedUrl,
            ]),
        ];
    }
}
