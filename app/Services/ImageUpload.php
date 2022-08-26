<?php

namespace App\Services;

use File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageUpload
{

    /**
     * To upload image with creating thumb
     * @param File $file
     * @param array $params contain ['originalPath', 'thumbPath', 'thumbHeight', 'thumbWidth', 'previousImage']
     */
    public static function uploadWithThumbImage($file, $params)
    {
        try {
            if (!empty($file) && !empty($params)) {

                $extension = $file->getClientOriginalExtension();
                $name = Str::random(20) . '.' . $extension;
                $storage = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'));
                // Make original path
                if (!$storage->exists($params['originalPath'])) {
                    $storage->makeDirectory($params['originalPath']);
                }

                // Make thumb path
                if (!$storage->exists($params['thumbPath'])) {
                    $storage->makeDirectory($params['thumbPath']);
                }

                $originalPath = $params['originalPath'] . $name;
                $thumbPath = $params['thumbPath'] . $name;
                // Store original image
                $storage->put($originalPath, file_get_contents($file), 'public');

                if (Image::make($file)->height() > Image::make($file)->width()){
                    $thumb = Image::make($file)->resize(null,$params['thumbHeight'], function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode($extension);
                } else {
                    $thumb = Image::make($file)->resize($params['thumbWidth'], null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode($extension);
                }

                $storage->put($thumbPath, (string) $thumb, 'public');

                // Delete previous image
                if ($params['previousImage'] != '') {
                    $originalImage = $params['originalPath'] . $params['previousImage'];
                    $thumbImage = $params['thumbPath'] . $params['previousImage'];
                    if ($storage->exists($originalImage)) {
                        $storage->delete($originalImage);
                    }
                    if ($storage->exists($thumbImage)) {
                        $storage->delete($thumbImage);
                    }
                }
                return [
                    'imageName' => $name,
                    'fileType' =>$extension,
                ];
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.IMAGE_UPLOAD_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }


    /**
     * To upload image with creating thumb
     * @param File $file
     * @param array $params contain ['originalPath', 'thumbPath', 'thumbHeight', 'thumbWidth', 'previousImage']
     */
    public static function uploadImage($file, $params, $postId)
    {
        try {
            if (!empty($file) && !empty($params)) {

                $extension = $file->getClientOriginalExtension();
                $fileData = $file->getClientOriginalName();

                $fileName = pathinfo($fileData, PATHINFO_FILENAME);
                $name = $fileName.'_'.$postId.'.' . $extension;
                $thumbName = $fileName.'_'.$postId.'_thumb.' . $extension;
                //$name = Str::random(20) . '.' . $extension;
                $storage = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'));
                // Make original path
                if (!$storage->exists($params['originalPath'])) {
                    $storage->makeDirectory($params['originalPath']);
                }

                // Make thumb path
                if (!$storage->exists($params['thumbPath'])) {
                    $storage->makeDirectory($params['thumbPath']);
                }

                $originalPath = $params['originalPath'] . $name;
                $thumbPath = $params['thumbPath'] . $thumbName;
                // Store original image
                $storage->put($originalPath, file_get_contents($file), 'public');

                if (Image::make($file)->height() > Image::make($file)->width()){
                    $thumb = Image::make($file)->resize(null,$params['thumbHeight'], function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode($extension);
                } else {
                    $thumb = Image::make($file)->resize($params['thumbWidth'], null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode($extension);
                }

                $storage->put($thumbPath, (string) $thumb, 'public');

                // Delete previous image
                if ($params['previousImage'] != '') {
                    $originalImage = $params['originalPath'] . $params['previousImage'];
                    $thumbImage = $params['thumbPath'] . $params['previousImage'];
                    if ($storage->exists($originalImage)) {
                        $storage->delete($originalImage);
                    }
                    if ($storage->exists($thumbImage)) {
                        $storage->delete($thumbImage);
                    }
                }
                return [
                    'thumbName' => $thumbName,
                    'imageName' => $name,
                    'fileType' =>$extension,
                ];
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.IMAGE_UPLOAD_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }


    public static function uploadVideo($file, $params, $postId = null)
    {
        try {

            if (!empty($file) && !empty($params)) {
                $extension = $file->getClientOriginalExtension();
                $fileData = $file->getClientOriginalName();
                $fileName = pathinfo($fileData, PATHINFO_FILENAME);
                if (!empty($postId)) {
                    $baseName = $fileName.'_'.$postId;
                } else {
                    $baseName = Str::random(20);
                }
                $name = $baseName . '.' . $extension;
                $thumbName = '';
                //$size = $file->getClientSize();
                //$storage = Storage::disk(Config::get('constant.FILESYSTEM_DRIVER'));
                $storage = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'));

                // Make original path
                if (!$storage->exists($params['originalPath'])) {
                    $storage->makeDirectory($params['originalPath']);
                }

                if (!$storage->exists($params['thumbPath'])) {
                    $storage->makeDirectory($params['thumbPath']);
                }                
                // Store original video
                $originalPath = $params['originalPath'] . $name;
                $storage->put($originalPath, file_get_contents($file), 'public');
                if (!empty($params['thumbName'])) {
                    $file = $params['thumbName'];
                    $base64_str = substr($file, strpos($file, ",")+1);
                    $decodedFile = base64_decode($base64_str);
                    $thumbName = $baseName.'.'.explode('/', mime_content_type($file))[1];
                    $thumbPath = $params['thumbPath'] . $thumbName;
                    $storage->put($thumbPath, $decodedFile, 'public');
                }
                //Delete previous video
                if ($params['previousVideo'] != '') {
                    $originalVideo = $params['originalPath'] . $params['previousVideo'];
                    if ($storage->exists($originalVideo)) {
                        $storage->delete($originalVideo);
                    }
                }
                return [
                    'imageName' => $name,
                    'fileType' => $extension,
                    'thumbName' => $thumbName
                ];
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.VIDEO_UPLOAD_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }


    /**
     * local upload video
     * as per use only testing
     */
    public static function uploadVideoApi($file, $params)
    {
        try {

            if (!empty($file) && !empty($params)) {
                // get extension for video
                $extension = $file->getClientOriginalExtension();
                // change video name using random function
                $name = Str::random(20) . '.' . $extension;
                // get video size
                // $size = $file->getClientSize();
                // set path for storing file
                $storage = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'));
                // Make original path
                if (!$storage->exists($params['originalPath'])) {
                    $storage->makeDirectory($params['originalPath']);
                }
                $originalPath = $params['originalPath'] . $name;
                
                // Store original video
                $storage->put($originalPath, file_get_contents($file), 'public');
                return [
                    'videoName' => $name,
                    //'size' => $size
                ];
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.VIDEO_UPLOAD_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }

    /**
     * To upload image with creating thumb
     * @param File $file
     * @param array $params contain ['originalPath', 'thumbPath', 'thumbHeight', 'thumbWidth', 'previousImage']
     */
    public static function uploadMedia($file, $params)
    {
        try {
            if (!empty($file) && !empty($params)) {

                $extension = $file->getClientOriginalExtension();                
                $name = !empty($params['fileName']) ? $params['fileName'] : Str::random(20) . '.' . $extension;
                $storage = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'));
                // Make original path
                if (!$storage->exists($params['originalPath'])) {
                    $storage->makeDirectory($params['originalPath']);
                }

                $originalPath = $params['originalPath'] . $name;
                $thumbPath = $params['thumbPath'] . $name;
                // Store original image
                $storage->put($originalPath, file_get_contents($file), 'public');

                // Delete previous image
                if ($params['previousImage'] != '') {
                    $originalImage = $params['originalPath'] . $params['previousImage'];
                    $thumbImage = $params['thumbPath'] . $params['previousImage'];
                    if ($storage->exists($originalImage)) {
                        $storage->delete($originalImage);
                    }
                    if ($storage->exists($thumbImage)) {
                        $storage->delete($thumbImage);
                    }
                }
                return [
                    'imageName' => $name,
                    'fileType' =>$extension,
                ];
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.IMAGE_UPLOAD_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }

}
