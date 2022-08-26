<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class PostGalleryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $OriginalImagePath = Config::get('constant.POST_ORIGINAL_PHOTO_UPLOAD_PATH');
        $ThumbImagePath = Config::get('constant.POST_THUMB_PHOTO_UPLOAD_PATH');
        $imageName = "";
        $thumbName = "";
        $videoName = "";

        if (in_array($this->file_type, Config::get('constant.IMAGE_EXTENSION'))){
            $imageName = !empty($this->file_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->file_name) : '';
            $thumbName = !empty($this->thumb_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->thumb_name) : '';
        }else if (in_array($this->file_type, Config::get('constant.VIDEO_EXTENSION'))){
            $videoName = !empty($this->file_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($OriginalImagePath.$this->file_name) : '';
            $thumbName = !empty($this->thumb_name) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($ThumbImagePath.$this->thumb_name) : '';
        }
    
       $arr = [];
       $arr['id'] = $this->id;
       $arr['fileName'] =  $imageName;
       $arr['thumbFile'] = $thumbName;
       $arr['videofile'] = $videoName;
       $arr['fileType'] = $this->file_type;
       $arr['fileLength'] =($this->file_length === null || $this->file_length == '') ? 0 : intval($this->file_length);
       $arr['fileSize'] = ($this->file_size === null || $this->file_size == '') ? 0 : intval($this->file_size);
       $arr['status']=$this->status;
       $arr['deletedAt']= $this->deleted_at;
       $arr['createdAt']= $this->created_at;
       $arr['updatedAt']= $this->updated_at;
       return $arr;
            //  'attach_type'=> if($){},
        
    }
}
