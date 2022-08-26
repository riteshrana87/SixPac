<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\GetFit;
use App\Models\Exercise;
use App\Models\Interests;
use App\Models\WorkoutType;
use App\Models\ExerciseDuration;
use App\Models\Equipment;
use App\Models\BodyParts;
use App\Models\AgeGroup;
use App\Models\FitnessLevel;
use App\Models\PlanDay;
use App\Models\PlanGoals;
use App\Models\PlanSport;

class ExerciseService
{

    public function __construct()
    {
        $this->exerciseOriginalImagePath = Config::get('constant.EXERCISE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->exerciseThumbImagePath = Config::get('constant.EXERCISE_THUMB_PHOTO_UPLOAD_PATH');
        $this->exerciseThumbImageHeight = Config::get('constant.EXERCISE_THUMB_PHOTO_HEIGHT');
        $this->exerciseThumbImageWidth = Config::get('constant.EXERCISE_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
    }

    /**
     * getFitList is used to get fit list
     *
     * @return array getfit list array
     * @author Spec Developer
     */
    public function getExercise(Request $request): ?array
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
            $totalRecords = Exercise::select('count(*) as allcount')->count();
            $exerciseObj = Exercise::with('workoutType:id,name')
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
                 $iconUrl = !empty($row->poster_image) ? Storage::disk($this->fileSystemCloud)->url($this->exerciseThumbImagePath.$row->poster_image) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' class='exercise__poster'>";
                if ($row->workoutType) {
                     $data_arr[$key]->workout_type_id = $row->workoutType->name ?? '-';
                }
                $data_arr[$key]->status = !empty($row->status) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/get-fit/exercises/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('business/get-fit/exercises/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/get-fit/exercises/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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

    /**
     * getExerciseData is used to get exercise related data for dropdown
     *
     * @return array return data array
     * @author Spec Developer
     */
    public function getExerciseData(): ?array
    {
        $interests = Interests::select('interest_name', 'id', 'icon_file')
        ->where('status', 1)->get()->toArray();

        $workoutTypes = WorkoutType::select('name', 'id', 'icon_file')
        ->where('status', 1)->get()->toArray();

        $durations = ExerciseDuration::where('status', 1)->pluck('duration', 'id')->toArray();
        $equipments = Equipment::select('name', 'id', 'icon_file')
        ->where('status', 1)->get()->toArray();

        $bodyParts = BodyParts::select('name', 'id', 'icon_file')
        ->where('status', 1)->get()->toArray();

        $ageGroups = AgeGroup::where('status', 1)->pluck('name', 'id')->toArray();
        $fitnessLevels = FitnessLevel::select('name', 'id', 'icon_file')
        ->where('status', 1)->get()->toArray();

        $exercises = Exercise::with('duration:id,duration')->where('status', 1)->orderByDesc('id')
        ->get()->toArray();

        // Workout plan master data code start
        $planGoals = PlanGoals::select('name', 'id', 'icon_file')
        ->where('status', 1)
        ->get()
        ->toArray();

        $planDurations = PlanDay::where('status', 1)
        ->pluck('name', 'id')
        ->toArray();

        $planSports = PlanSport::select('name', 'id', 'icon_file')
        ->where('status', 1)
        ->get()
        ->toArray();
        // Workout plan master data code end

        return [
            'interests' => $interests,
            'workoutTypes' => $workoutTypes,
            'durations' => $durations,
            'equipments' => $equipments,
            'bodyParts' => $bodyParts,
            'ageGroups' => $ageGroups,
            'fitnessLevels' => $fitnessLevels,
            'exercise' => $exercises,
            'gender' => config('constant.GETFIT_GENDER'),
            'location' => config('constant.GETFIT_LOCATION'),
            'goals' => $planGoals,
            'planDurations' => $planDurations,
            'planSports' => $planSports,
        ];
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
                    'originalPath' => $this->exerciseOriginalImagePath,
                    'thumbPath' => $this->exerciseThumbImagePath,
                    'thumbHeight' => $this->exerciseThumbImageHeight,
                    'thumbWidth' => $this->exerciseThumbImageWidth,
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
                    'originalPath' => $this->exerciseOriginalImagePath,
                    'thumbPath' => $this->exerciseThumbImagePath,
                    'thumbHeight' => $this->exerciseThumbImageHeight,
                    'thumbWidth' => $this->exerciseThumbImageWidth,
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
            $originalPath = $this->exerciseOriginalImagePath.$imageName;
            $thumbPath = $this->exerciseThumbImagePath.$imageName;
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
    public function deleteVideoAndThumb($exercise): ?bool
    {
        if (empty($exercise->video_name) || empty($exercise->video_thumb)) return false;
        $isVideoDeleted = $isThumbDeleted = false;
        $originalPath = $this->exerciseOriginalImagePath.$exercise->video_name;
        $thumbPath = $this->exerciseThumbImagePath.$exercise->video_thumb;
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
    public function syncExerciseData($exercise, $input): ?Exercise
    {
        if (!empty($input['interests'])) {
            $exercise->interests()->sync($input['interests']);
        }
        if (!empty($input['equipment'])) {
            $exercise->equipments()->sync($input['equipment']);
        }
        if (!empty($input['body_part'])) {
            $exercise->bodyParts()->sync($input['body_part']);
        }
        if (!empty($input['age_group'])) {
            $exercise->ageGroups()->sync($input['age_group']);
        }
        if (!empty($input['fitness_level'])) {
            $exercise->fitnessLevels()->sync($input['fitness_level']);
        }
        return $exercise;
    }

    /**
     * createExercise is used to save exercise data
     *
     * @param  Request $request input parameter
     * @return object  \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function createExercise(Request $request)
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
            $exercise = Exercise::create($input);
            if ($exercise) {
                $this->syncExerciseData($exercise, $input);
            }
            return $exercise;
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }

    /**
     * updateExercise is used to update exercise data
     *
     * @param  Request $request input parameter
     * @return object  \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function updateExercise(Request $request)
    {
        try {
            $input = $request->all();
            if (empty($input['id'])) {
                return false;
            }
            $exercise = Exercise::findOrFail($input['id']);
            if ($request->hasFile('poster_image') && $request->file('poster_image')->isValid()) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['poster_image'] = $userPhoto['imageName'];
                $this->deleteImageAndThumb($exercise->poster_image ?? null);
            }

            if ($request->hasFile('video_name') && $request->file('video_name')->isValid()) {
                $isvalidMedia = $this->uploadVideo($request);
                if ($isvalidMedia === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['video_name'] = $isvalidMedia['imageName'];
                $input['video_thumb'] = $isvalidMedia['thumbName'];
                $this->deleteVideoAndThumb($exercise ?? null);
            }
            $exercise->fill($input);
            if ($exercise->save()) {
                $this->syncExerciseData($exercise, $input);
            }
            return $exercise;
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return false;
        }
    }

    /**
     * deleteExercise is used to delete exercise details
     *
     * @param  integer $id exercise primary id
     * @return bool    true if exercise deleted otherwise false
     * @author Spec Developer
     */
    public function deleteExercise($id): ?bool
    {
        try {
            $exercise = Exercise::findOrFail($id);
            if ($exercise) {
                $this->deleteImageAndThumb($exercise->poster_image ?? null);
                $this->deleteVideoAndThumb($exercise ?? null);
                $exercise->interests()->detach();
                $exercise->equipments()->detach();
                $exercise->bodyParts()->detach();
                $exercise->ageGroups()->detach();
                $exercise->fitnessLevels()->detach();
                $exercise->delete();
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
}