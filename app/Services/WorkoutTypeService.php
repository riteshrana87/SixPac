<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\GetFit;
use App\Models\WorkoutType;

class WorkoutTypeService
{

    public function __construct()
    {
        $this->workoutOriginalImagePath = Config::get('constant.WORKOUT_TYPE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImagePath = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImageHeight = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_HEIGHT');
        $this->workoutThumbImageWidth = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
    }

    /**
     * uploadImage is used to upload workout category icon image
     *
     * @param  Request $request request parameter
     * @return mix     bool or array
     * @author Spec Developer
     */
    public function uploadImage(Request $request) {
        try {
            if ($request->hasFile('icon_file') && $request->file('icon_file')->isValid()) {
                $file = $request->file('icon_file');
                $params = [
                    'originalPath' => $this->workoutOriginalImagePath,
                    'thumbPath' => $this->workoutThumbImagePath,
                    'thumbHeight' => $this->workoutThumbImageHeight,
                    'thumbWidth' => $this->workoutThumbImageWidth,
                    'previousImage' => ''
                ];
                return ImageUpload::uploadWithThumbImage($request->file('icon_file'), $params);
            }
            return false;
        } catch (\Exception $e) {
           return false;
        }
    }

    /**
     * deleteImageAndThumb is used to delete image and thumb
     *
     * @param  string $imageName image name that you want to delete
     * @return bool   return true if image and thumb deleted
     * @author Spec Developer
     */
    public function deleteImageAndThumb($imageName): ?bool
    {
        $isImageDeleted = $isThumbDeleted = false;
        if (!empty($imageName)) {
            $originalPath = $this->workoutOriginalImagePath.$imageName;
            $thumbPath = $this->workoutThumbImagePath.$imageName;
            $isImageDeleted = Storage::disk($this->fileSystemCloud)->exists($originalPath) ? Storage::disk($this->fileSystemCloud)->delete($originalPath) : false;
            $isThumbDeleted = Storage::disk($this->fileSystemCloud)->exists($thumbPath) ? Storage::disk($this->fileSystemCloud)->delete($thumbPath) : false;
        }
        return ($isImageDeleted && $isThumbDeleted) ? true : false;
    }

    /**
     * storeType is used to store workout category data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function storeType(Request $request): ?array
    {
        try {
            $input = $request->all();
            if ($request->hasFile('icon_file')) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['icon_file'] = $userPhoto['imageName'];
            }
            $workoutType = WorkoutType::create($input);
            return ['status' => true,'message'=>'','data'=>$workoutType];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }
    }

    /**
     * updateType is used to update workout category data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function updateType(Request $request): ?array
    {
        try {
            $input = $request->all();
            $workoutType = workoutType::findOrFail($request->id);
            if ($request->hasFile('icon_file')) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['icon_file'] = $userPhoto['imageName'];
                $this->deleteImageAndThumb($workoutType->icon_file ?? null);
            }
            $workoutType->fill($input);
            $status = $workoutType->save() ? true : false;
            return ['status' => $status,'message'=> $status ? 'Workout type has been updated!.' : 'Unable to update workout type!.','data'=>$workoutType];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }
    }

    /**
     * getFitList is used to get fit list
     *
     * @return array getfit list array
     * @author Spec Developer
     */
    public function getFitList(): ?array
    {
        return GetFit::where('status', 1)->pluck('name', 'id')->toArray();
    }
}