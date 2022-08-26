<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\GetFit;
use App\Models\Workout;

class WorkoutService
{

    public function __construct()
    {
        $this->workoutOriginalImagePath = Config::get('constant.WORKOUT_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImagePath = Config::get('constant.WORKOUT_THUMB_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImageHeight = Config::get('constant.WORKOUT_THUMB_PHOTO_HEIGHT');
        $this->workoutThumbImageWidth = Config::get('constant.WORKOUT_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
    }

    /**
     * getFitList is used to get fit list
     *
     * @return array getfit list array
     * @author Spec Developer
     */
    public function getWorkout(Request $request): ?array
    {
        $response = [
            "draw" => 0,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => []
        ];
        if ($request->ajax()) {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowPerPage = $request->get("length");
            $searchValue = $request->get('search')['value'] ?? '';
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $sortOrder = $order_arr[0]['dir'];
            $totalRecords = Workout::select('count(*) as allcount')->count();
            $exerciseObj = Workout::with('workoutType:id,name')
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->where('name', 'like', '%'.$searchValue.'%')
                    ->OrWhereHas('workoutType', function ($query2) use ($searchValue) {
                        $query2->where('name', 'like', '%'.$searchValue.'%');
                    });
                    $position = stripos("Active Deactive",$searchValue);
                    if ($position!==false) {
                        $query1->OrWhereIn('status', ($position < 6) ? [0,1] : [0]);
                    }
                });
            });
            $totalFilteredRows = $exerciseObj->count();
            $exerciseData = $exerciseObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();
            $data_arr = [];
            $data_arr = $exerciseData;
            foreach($exerciseData as $key => $row) {
                 $iconUrl = !empty($row->poster_image) ? Storage::disk($this->fileSystemCloud)->url($this->workoutThumbImagePath.$row->poster_image) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' class='exercise__poster'>";
                if ($row->workoutType) {
                     $data_arr[$key]->workout_type_id = $row->workoutType->name ?? '-';
                }
                $data_arr[$key]->status = !empty($row->status) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/get-fit/workout/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('business/get-fit/workout/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/get-fit/workout/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
                $data_arr[$key]->action = $btn;
            }
            $response = [
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
            ];
        }
        echo json_encode($response); exit;
    }

    public function prepareWorkoutExercise($exercise)
    {
         $fileUrl = !empty($exercise->poster_image) ? Storage::disk($this->fileSystemCloud)->url(Config::get('constant.EXERCISE_THUMB_PHOTO_UPLOAD_PATH').$exercise->poster_image) : '';
        ob_start();
        ?>
<div class="card mb-3 exercise__box">
    <label class="exercise-label" for="exercise_<?=$exercise->id?>">
        <div class="row no-gutters">
            <div class="col-md-2">
                <img src="<?=$fileUrl?>" alt="Card image cap" width="100%">
            </div>
            <div class="col-md-10">
                <div class="card-body">
                    <h5 class="card-title"><?=$exercise->name ?? ''?></h5>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="card-text"><?=$exercise->duration->duration?></span></p>
                        <span class="chk-container">
                            <input type="checkbox" name="exercise[]" id="exercise_<?=$exercise->id?>"
                                class="chk-exercise" data-name="exercise" value="<?=$exercise->id?>">
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </label>
</div>
<?php
        $content = ob_get_clean();
        return $content;
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
            if ($request->hasFile('poster_image') && $request->file('poster_image')->isValid()) {
                $file = $request->file('poster_image');
                $params = [
                    'originalPath' => $this->workoutOriginalImagePath,
                    'thumbPath' => $this->workoutThumbImagePath,
                    'thumbHeight' => $this->workoutThumbImageHeight,
                    'thumbWidth' => $this->workoutThumbImageWidth,
                    'previousImage' => ''
                ];
                return ImageUpload::uploadWithThumbImage($request->file('poster_image'), $params);
            }
            return false;
        } catch (\Exception $e) {
           return false;
        }
    }

    /**
     * uploadImage is used to upload workout category icon image
     *
     * @param  Request $request request parameter
     * @return mix     bool or array
     * @author Spec Developer
     */
    public function uploadVideo(Request $request) {
        try {
            if ($request->hasFile('video_name') && $request->file('video_name')->isValid()) {
                $params = [
                    'originalPath' => $this->workoutOriginalImagePath,
                    'thumbPath' => $this->workoutThumbImagePath,
                    'thumbHeight' => $this->workoutThumbImageHeight,
                    'thumbWidth' => $this->workoutThumbImageWidth,
                    'thumbName' => $request->get('video_thumb') ?? '',
                    'previousVideo' => ''
                ];
                return ImageUpload::uploadVideo($request->file('video_name'), $params);
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
     * deleteVideoAndThumb is used to delete video and thumb
     *
     * @param  string $imageName image name that you want to delete
     * @return bool   return true if image and thumb deleted
     * @author Spec Developer
     */
    public function deleteVideoAndThumb($workout): ?bool
    {
        if (empty($workout->video_name) || empty($workout->video_thumb)) return false;
        $isVideoDeleted = $isThumbDeleted = false;
        $originalPath = $this->workoutOriginalImagePath.$workout->video_name;
        $thumbPath = $this->workoutThumbImagePath.$workout->video_thumb;
        $isVideoDeleted = Storage::disk($this->fileSystemCloud)->exists($originalPath) ? Storage::disk($this->fileSystemCloud)->delete($originalPath) : false;
        $isThumbDeleted = Storage::disk($this->fileSystemCloud)->exists($thumbPath) ? Storage::disk($this->fileSystemCloud)->delete($thumbPath) : false;
        return ($isVideoDeleted && $isThumbDeleted) ? true : false;
    }

    /**
     * syncExerciseData is used to sync exercise data
     *
     * @param  object $exercise exercise object
     * @param  array  $input    input parameter
     * @return object exercise object
     * @author Spec Developer
     */
    public function syncWorkoutData($workout, $input): ?Workout
    {
        if (!empty($input['exercise'])) {
            $workout->exercises()->sync($input['exercise']);
        }
        if (!empty($input['equipment'])) {
            $workout->equipments()->sync($input['equipment']);
        }
        if (!empty($input['body_part'])) {
            $workout->bodyParts()->sync($input['body_part']);
        }
        if (!empty($input['fitness_level'])) {
            $workout->fitnessLevels()->sync($input['fitness_level']);
        }
        return $workout;
    }

    /**
     * createWorkout is used to save workout data
     *
     * @param  Request $request input parameter
     * @return object  \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function createWorkout(Request $request)
    {
        try {
            $input = $request->all();
            if ($request->hasFile('poster_image') && $request->file('poster_image')->isValid()) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['poster_image'] = $userPhoto['imageName'];
            }

            if ($request->hasFile('video_name') && $request->file('video_name')->isValid()) {
                $isvalidMedia = $this->uploadVideo($request);
                if ($isvalidMedia === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['video_name'] = $isvalidMedia['imageName'];
                $input['video_thumb'] = $isvalidMedia['thumbName'];
            }
            $workout = Workout::create($input);
            if ($workout) {
                $this->syncWorkoutData($workout, $input);
            }
            return $workout;
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }

    /**
     * createWorkout is used to save workout data
     *
     * @param  Request $request input parameter
     * @return object  \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function updateWorkout(Request $request)
    {
        try {
            $input = $request->all();
            $workout = Workout::findOrFail($input['id']);
            if ($request->hasFile('poster_image') && $request->file('poster_image')->isValid()) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['poster_image'] = $userPhoto['imageName'];
                $this->deleteImageAndThumb($workout->poster_image ?? null);
            }

            if ($request->hasFile('video_name') && $request->file('video_name')->isValid()) {
                $isvalidMedia = $this->uploadVideo($request);
                if ($isvalidMedia === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['video_name'] = $isvalidMedia['imageName'];
                $input['video_thumb'] = $isvalidMedia['thumbName'];
                $this->deleteVideoAndThumb($workout ?? null);
            }
            $workout->fill($input);
            if ($workout->save()) {
                $this->syncWorkoutData($workout, $input);
            }
            return $workout;
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }

     /**
     * deleteWorkout is used to delete workout details
     *
     * @param  integer $id workout primary id
     * @return bool    true if workout deleted otherwise false
     * @author Spec Developer
     */
    public function deleteWorkout($id): ?bool
    {
        try {
            $workout = Workout::findOrFail($id);
            if ($workout) {
                $this->deleteImageAndThumb($workout->poster_image ?? null);
                $this->deleteVideoAndThumb($workout ?? null);
                $workout->exercises()->detach();
                $workout->equipments()->detach();
                $workout->bodyParts()->detach();
                $workout->fitnessLevels()->detach();
                $workout->delete();
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
}