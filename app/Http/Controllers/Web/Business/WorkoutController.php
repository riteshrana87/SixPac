<?php

namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\Services\WorkoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Workout;
use App\Services\ExerciseService;
use App\Http\Requests\Business\ExerciseRequest;
use App\Http\Requests\Business\WorkoutRequest;
use App\Helpers\Helper;

class WorkoutController extends Controller
{

    public function __construct(){
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->workoutOriginalImagePath = Config::get('constant.WORKOUT_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImagePath = Config::get('constant.WORKOUT_THUMB_PHOTO_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->workoutService = new WorkoutService();
        $this->exerciseService = new ExerciseService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data['page_title'] = 'Workout';
        if ($request->ajax()) {
            $this->workoutService->getWorkout($request);
        }
        return view('business.workout.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $exerciseData = $this->exerciseService->getExerciseData();
        return view('business.workout.manage',compact('data','exerciseData'));
    }

    public function createExcercise() {
        $data = [];
        $exerciseData = $this->exerciseService->getExerciseData();
        return view('business.workout.add_exercise',compact('data','exerciseData'));
    }

    public function saveExcercise(ExerciseRequest $request)
    {
        $response = [];
        try {
            $exercise = $this->exerciseService->createExercise($request);
            if (!$exercise) {
                throw new \Exception('Unable to add exercise!');
            }
            $data = $this->workoutService->prepareWorkoutExercise($exercise);
            $response = ['status'=>true,'message'=>'Exercise has been added!','data'=>$data];
        } catch (\Exception $e) {
            $response = ['status'=>false,'message'=>$e->getMessage(),'data'=>[]];
        }
        echo json_encode($response);die;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkoutRequest $request)
    {
        try {
            $isValid = $this->workoutService->createWorkout($request);
            if ($isValid) {
                Alert::success('Success', 'Workout has been added!.', 'success');
                return redirect('business/get-fit/workout');
            } else {
                Alert::error('Error', 'Unable to add workout.', 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage(), 'error');
        }
        return redirect()->back()->with('error', 'Unable to add workout.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->object_id;
        $row = Workout::with(['workoutType:id,name','user:id,user_name,role','duration:id,duration','equipments','bodyParts','exercises','fitnessLevels'])->findOrFail($id);
        if ($row->bodyParts) {
            $bodyParts = Helper::objectToArray($row->bodyParts);
            $bodyParts = array_column($bodyParts,'name');
            $row['bodyParts'] = '<label class="label label-success mb-2">'.implode('</label><label class="label label-success mb-2">', $bodyParts).'</label>';
        }
        if ($row->equipments) {
            $equipments = Helper::objectToArray($row->equipments);
            $equipments = array_column($equipments,'name');
            $row['equipments'] = '<label class="label label-success mb-2">'.implode('</label><label class="label label-success mb-2">', $equipments).'</label>';
        }
        if ($row->exercises) {
            $exercises = Helper::objectToArray($row->exercises);
            $exercises = array_column($exercises,'name');
            $row['exercises'] = '<label class="label label-success mb-2">'.implode('</label><label class="label label-success mb-2">', $exercises).'</label>';
        }
        if ($row->fitnessLevels) {
            $fitnessLevels = Helper::objectToArray($row->fitnessLevels);
            $fitnessLevels = array_column($fitnessLevels,'name');
            $row['fitnessLevels'] = '<label class="label label-success mb-2">'.implode('</label><label class="label label-success mb-2">', $fitnessLevels).'</label>';
        }

        $url = Storage::disk($this->fileSystemCloud)->url($this->workoutOriginalImagePath.$row->poster_image) ?? asset('backend/assets/images/no-icon.png');
        $row['poster_image'] = $url;
        $row['video_thumb'] = Storage::disk($this->fileSystemCloud)->url($this->workoutThumbImagePath.$row->video_thumb) ?? asset('backend/assets/images/no-icon.png');
        return view('business.workout.view',compact('row'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
         $data = Workout::with(['exercises','equipments','bodyParts','fitnessLevels'])
        ->where('id',$id)->first();
        $exerciseData = $this->exerciseService->getExerciseData();
        return view('business.workout.manage', compact('data', 'exerciseData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Workout  $workout
     * @return \Illuminate\Http\Response
     */
    public function update(WorkoutRequest $request, Workout $workout)
    {
        try {
            $isValid = $this->workoutService->updateWorkout($request);
            if ($isValid) {
                Alert::success('Success', 'Workout has been updated!.', 'success');
                return redirect('business/get-fit/workout');
            } else {
                Alert::error('Error', 'Unable to updated workout.', 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage(), 'error');
        }
        return redirect()->back()->with('error', 'Unable to updated workout.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer $id workout primary id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (empty($id)) {
                throw new \Exception('Invalid workout id.');
            }
            $isDeleted = $this->workoutService->deleteWorkout($id);
            if ($isDeleted) {
                Alert::success('Success', 'Workout has been deleted!.', 'success');
            }
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage(), 'error');
        }
        return redirect('business/get-fit/workout');
    }
}