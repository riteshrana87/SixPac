<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExcerciseVideoResource;
use App\Http\Resources\GetFitExerciseResource;
use App\Jobs\TranscodeVideo;
use App\Models\DeviceToken;
use App\Models\Exercisables;
use App\Models\Exercise;
use App\Models\Notification;
use App\Models\WorkoutDetail;
use App\Models\WorkoutMedia;
use App\Services\ImageUpload;
use App\Services\VideoService;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GetFitExcerciseController extends Controller
{
    public function __construct()
    {
        $this->excerciseOriginalImagePath = Config::get('constant.EXCERCISE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->excerciseThumbImagePath = Config::get('constant.EXCERCISE_THUMB_PHOTO_UPLOAD_PATH');
        $this->excerciseThumbImageHeight = Config::get('constant.EXCERCISE_THUMB_PHOTO_HEIGHT');
        $this->excerciseThumbImageWidth = Config::get('constant.EXCERCISE_THUMB_PHOTO_WIDTH');
    }

    public function addExcercise(Request $request)
    {
        Log::info('Create Excercise');
        
        try {
            $excercise = new WorkoutDetail();
            $excercise->getfit_id = $request->getfitId;
            $excercise->categories_id = $request->categoriesId;
            $excercise->designated_id = $request->designatedId;
            $excercise->body_parts_id = $request->bodyPartsId;
            $excercise->workouts_type_id = $request->workoutsTypeId;
            $excercise->name = $request->name;
            $excercise->description = $request->description;
            $excercise->is_public = $request->isPublic;
            $excercise->created_by = Auth::user()->id;
            $excercise->updated_by = Auth::user()->id;
            $excercise->Workout_flag = 1;
            $excercise->save();

            /* upload file and video */
            if (!empty($request->file('file'))) {
                $imageParams = [
                    'originalPath' => $this->excerciseOriginalImagePath,
                    'thumbPath' => $this->excerciseThumbImagePath,
                    'thumbHeight' => $this->excerciseThumbImageHeight,
                    'thumbWidth' => $this->excerciseThumbImageWidth,
                    'previousImage' => $request->hidden_photo,
                ];
                foreach ($request->file('file') as $file) {
                    $excerciseGallery = new WorkoutMedia();
                    $excerciseGallery->workout_mediable_id = $excercise->id;
                    
                    // Upload Excercise Photo
                    if (!empty($file) && $file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileMediaOrder = $file->getClientOriginalName();
                        $fileMediaOrderName = pathinfo($fileMediaOrder, PATHINFO_FILENAME);
                        if (in_array($extension, Config::get('constant.IMAGE_EXTENSION')))
                        {
                            $excercisePhoto = ImageUpload::uploadImage($file, $imageParams, $excercise->id);
                            $postgallery['thumb_name'] = $excercisePhoto['thumbName'];
                        }
                        else if (in_array($extension, Config::get('constant.VIDEO_EXTENSION')))
                        {
                            $params = [
                                        'originalPath' => $this->excerciseOriginalImagePath,
                                        'thumbPath' => $this->excerciseThumbImagePath,
                                        'thumbHeight' => $this->excerciseThumbImageHeight,
                                        'thumbWidth' => $this->excerciseThumbImageWidth,
                                        'previousVideo' =>  "",
                                     ];
                            $postPhoto = ImageUpload::uploadVideo($file, $params, $excercise->id);

                            $fileData = $file->getClientOriginalName();
                            
                            $fileName = pathinfo($fileData, PATHINFO_FILENAME);
                            $excerciseGallery['file_name'] = $fileData;
                            $excerciseGallery['size'] = $request->size;
                            $excerciseGallery['file_type'] = $extension;//$request->type;

                            // if (!empty($request->file('videoThumbImage'))) {
                            //     foreach ($request->file('videoThumbImage') as $thumbfile) {
                            //         $ThumbImageExt = $thumbfile->getClientOriginalExtension();

                            //         $thumbData = $thumbfile->getClientOriginalName();
                            //         $thumbName = pathinfo($thumbData, PATHINFO_FILENAME);
                            //         if (in_array($ThumbImageExt, Config::get('constant.IMAGE_EXTENSION')))
                            //             {
                            //                 if($thumbName == $fileName){
                            //                     $videoThumbImage = ImageUpload::uploadImage($thumbfile, $imageParams, $post->id);
                            //                     if(!empty($videoThumbImage)){
                            //                         $postgallery['thumb_name'] = $videoThumbImage['thumbName'];
                            //                     }
                            //                 }
                            //             }
                            //     }
                            // }
                        }

                        // if ($postPhoto === false) {
                        //     DB::rollback();
                        //     $response = [];
                        //     $response['message'] = "error";
                        //     $response['status'] = 500;
                        //     Log::info(trans('log-messages.POST_IMAGE_UPLOAD_ERROR_MESSAGE'));
                        //     return response()->json($response, 500);
                        // }
                        // $postgallery['media_order'] = $fileMediaOrderName;
                        // $postgallery['file_name'] = $fileData;
                        // $postgallery['file_type'] = $postPhoto['fileType'];
                   }
                   //dd($postgallery);
                   $Workout = WorkoutDetail::find($excercise->id);
                    $com_data = $Workout->comments()->save($excerciseGallery);
                   //$excerciseGallery->save();
                   Log::info('Store image in gallery');
               }
            }
            
            $user = Auth::user();
            /*Start Store notification data*/
            $notificationData = [];
            $following = $user->checkSendNotificationToAddPost()->pluck('follower_id')->toArray();
            foreach ($following as $followinUser) {
                $notificationRecode = array(
                    'sender_id' => Auth::user()->id,
                    'receiver_id' => $followinUser,
                    'notification_type' => 'excercise-add',
                    'post_id' => $excercise->id,
                    'status' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                array_push($notificationData,$notificationRecode);
            }
            Notification::insert($notificationData);
            /*End Store notification data*/

            /*Start send push notification*/
                $deviceTokens = DeviceToken::whereIn('user_id', $following)->pluck('device_token')->toArray();
                if(!empty($deviceTokens)){
                    $notificationData = array(
                        'deviceToken' => $deviceTokens,
                        //'title' => Auth::user()->username,
                        'message' => Auth::user()->user_name . '  Add New Excercise',
                        'postId' => $excercise->id,
                        // 'icon' => !empty($gallery_data->file) ? Storage::disk('s3')->url($ThumbImagePath.$gallery_data->file) : '',
                    );
                    Helper::sendPushNotification($notificationData);
                }
            /*End send push notification*/

            Log::info('Add Excercise successfully');
            return successResponseWithoutData(trans('api-message.ADD_EXCERCISE_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            Log::info('Edit Food details :: message :'.$message);
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }


    /*
     @Author : Ritesh Rana
     @Desc   : Upload Video in chunk
     @Input  : Request  $request
     @Output : Illuminate\Http\Response
     @Date   : 07/06/2022
     */
    public function uploadExcerciseChunkVideo(Request $request)
    {
        try {
            $media_id = $request->mediaId;
            $start = $request->start;
            $end = $request->end;

            DB::beginTransaction();
            $video = WorkoutMedia::find($media_id);
            if (!$video){
                return response()->json([
                    'status' => 0,
                    'message' => trans('api-message.VIDEO_NOT_FOUND'),
                ]);
            }

            $address = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path($this->excerciseOriginalImagePath.$video->file_name);
            $size=0;
            if(file_exists($address)){
                $size=filesize($address);
            }

            if ($size == 0){
                $progressPercent = "0";
            } else{
                $progressPercent = ($size/($video->size))*100;
            }

            if ($start < $size){
                return response()->json([
                    'status' => 0,
                    'message' => trans('api-message.ALREADY_UPLOAD_CHUNK'),
                ]);
            }

            if ($end == $size){
                return response()->json([
                    'status' => 0,
                    'message' => trans('api-message.ALREADY_UPLOAD_CHUNK'),
                ]);
            }

            $message = trans('api-message.VIDEO_UPLOAD_IS_PROGRESS');
            $video->chunkPointer = $size;
            $video->progressPercent = number_format($progressPercent,2,'.','');
            $data = new ExcerciseVideoResource($video);
            unset($video->chunkPointer);
            unset($video->progressPercent);
            $uploadManager = new \UploadManager\Upload('media');
            
            //add callback : remove uploaded chunks on error
            $uploadManager->afterValidate(function ($chunk) {
                $address = ($chunk->getSavePath() . $chunk->getNameWithExtension());
                if ($chunk->hasError() && file_exists($address)) {
                    //remove current chunk on error
                    @unlink($address);
                }
            });


            //add callback : update database's log
            $uploadManager->afterUpload(function ($chunk) use ($video, $end, &$data) {
                
            $completed = ($video->size == $chunk->getSavedSize());

                if ($completed) {
                    $videoHelper = new VideoService();

                    $params = [
                        'fileName' => $video->file,
                        'originalPath' => Config::get('constant.EVENT_VIDEO_TEMP_UPLOAD_PATH'),
                        'imageOriginalPath' => Config::get('constant.EVENT_VIDEO_THUMB_UPLOAD_PATH'),
                    ];

                    $response = $videoHelper->setChunkVideo($params);
                    if ($response['status'] == 1) {

                        $info = $videoHelper->getInfo();
                        if (isset($info['status']) && $info['status'] == 0) {
                            return [
                                'status' => $info['status'],
                                'message' => $info['message'],
                            ];
                        }

                        $video->lengh = $info['duration'];
                        $video->thumb_path = basename($info['thumb']);

                        $videoInfo = array(
                            'id' => $video->id,
                            'file_name' => $info['filename'],
                            'video_url' => $info['file'],
                            'video_name' => basename($info['file']),
                            'thumb_url' => $info['thumb'],
                            'thumb_name' => basename($info['thumb']),
                            'file_size' => $info['filesize'],
                            'file_type' => $info['fileextension'],
                            'duration' => $info['duration'],
                            'current_width' => $info['width'],
                            'current_height' => $info['height'],
                            'bit_rate' => $info['bit_rate'],
                            'frame_rate' => $info['r_frame_rate'],
                            'rotation' => $info['rotation'],
                            'codecid' => $info['codecid'],
                            'codec_name' => $info['codec_name'],
                            'level' => $info['level'],
                            'profile' => $info['profile'],
                            'is_transcoded' => Config::get('constant.TRANSCODING_PENDING_VIDEO_STATUS'),
                        );
                        

                        // Dispatch job to process the video
                        $this->dispatch(new TranscodeVideo($videoInfo));
                    }
                    Log::info('File Upload and Thumb creation complete');
                    $message = trans('api-message.VIDEO_UPLOAD_SUCCESSFULLY');

                }

                $video->save();

                DB::commit();

                $video->chunkPointer = intval($chunk->getSavedSize());
                $video->progressPercent = number_format(($end / ($video->size)) * 100, 2, '.','');
                $data = new ExcerciseVideoResource($video);
            });

            $chunks = $uploadManager->upload(
                Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path($this->excerciseThumbImagePath),
                $video->file,
                false,
                $start,
                $end - $start + 1
            );

            return response()->json([
                'status' => 1,
                'message' => $message,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return response()->json([
                'status' => 0,
                'message' => trans('api-message.DEFAULT_ERROR_MESSAGE'),
            ], 500);
        } catch (\UploadManager\Exceptions\Upload $exception) {
            //send bad request error
            if (!empty($exception->getChunk())) {
                $chunk = $exception->getChunk();
                DB::rollback();
                Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                    '<Message>' => $exception->getMessage(),
                ]));
                return response()->json([
                    'status' => 0,
                    'message' => trans('api-message.DEFAULT_ERROR_MESSAGE'),
                ], 500);
            } else {
                DB::rollback();
                Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                    '<Message>' => $exception->getMessage(),
                ]));
                return response()->json([
                    'status' => 0,
                    'message' => trans('api-message.DEFAULT_ERROR_MESSAGE'),
                ], 500);
            }
        }
    }
/*
     @Author : Ritesh Rana
     @Desc   : get Exercise by filter
     @Input  : Request  $request
     @Output : Illuminate\Http\Response
     @Date   : 07/07/2022
     */
    public function getExerciseListByBodyPart(Request $request)
    {
        try {

            if (!empty($request->page)) {
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
            $exerciseId = [];
            if(!empty($request->bodyPart)){
                $exerciseId = Exercisables::where('exercisable_type','App\Models\BodyParts')->where('exercisable_id',$request->bodyPart)->groupBy('exercise_id')->pluck('exercise_id')->toArray();
            }    
        /*Start Create query for count Exercise data */
            $exerciseCount = Exercise::where("status",1);
            if(!empty($request->bodyPart)){
                $exerciseCount->whereIn('id',$exerciseId);
            }
            if(!empty($request->byType)){
                $exerciseCount->where('workout_type_id',$request->byType);
            }
            $exeCount= $exerciseCount->get();
        /*End Create query for count Exercise data */
            $numrows = count($exeCount);    
        /*Start Create query for Get Exercise data */
            $exerciseList = Exercise::where("status",1)->with(['interests','equipments','bodyParts','ageGroups','fitnessLevels','workoutType']);
            if(!empty($request->bodyPart)){
                $exerciseList->whereIn('id',$exerciseId);
            }
            if(!empty($request->byType)){
                $exerciseList->where('workout_type_id',$request->byType);
            }
            $exercise = $exerciseList->orderBy('created_at', 'DESC')
            ->skip($offset)
            ->take($perpage)
            ->get();
        /*End Create query for Get Exercise data */
        $exerciseData = GetFitExerciseResource::collection($exercise);
        $nextPage = LengthPager::makeLengthAware($exerciseData, $numrows, $perpage, []);
        
        $data = array(
            'totalCount' => $numrows,
            'perpage' => $perpage,
            'nextPage' => $nextPage,
            'exerciseData' => $exerciseData
        );
        Log::info('Excercise list fetched successfully');
        return successResponse(trans('api-message.EXCERCISE_LIST_FETCHED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to get Excercise due to err: ' . $e->getMessage());
            return errorResponse(trans('api-message.UNABLE_TO_GET_EXCERCISE'),STATUS_CODE_NOT_FOUND);
        }
    }

    
}
