<?php
namespace App\Http\Controllers\Web\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\Models\WorkoutCategory;
use Illuminate\Support\Str;
use DataTables;
use App\Models\Exercise;
use App\Services\ExerciseService;
use App\Models\ExerciseParam;
use App\Http\Requests\Business\ExerciseRequest;
use App\Models\BodyParts;


class ExerciseController extends Controller
{

	public function __construct(){
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->exerciseOriginalImagePath = Config::get('constant.EXERCISE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->exerciseThumbImagePath = Config::get('constant.EXERCISE_THUMB_PHOTO_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->exerciseService = new ExerciseService();
    }

    /**
     * index is used to list records
     *
     * @param  Request $request input parameter
     * @return Response
     * @author Spec Developer
     */
    public function index   (Request $request)
    {
        $data['page_title'] = 'Exercises';
        if ($request->ajax()) {
            $this->exerciseService->getExercise($request);
        }
    	return view('business.exercise.list',$data);
    }

    /**
     * [create description]
     * @author Spec Developer
     * @return [type] [description]
     */
    public function create()
    {
        $exerciseData = $this->exerciseService->getExerciseData();
        $data = [];
        return view('business.exercise.manage', compact('data', 'exerciseData'));
    }

    /**
     * saveExercise is used to save exercise data
     *
     * @param  ExerciseRequest $request input parameter
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
     public function saveExercise(ExerciseRequest $request)
    {
        try {
            $isValid = $this->exerciseService->createExercise($request);
            if ($isValid) {
                Alert::success('Success', 'Exercise has been added!.', 'success');
                return redirect('business/get-fit/exercises');
            }
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage(), 'error');
        }
        return redirect()->back()->with('error', 'Unable to add exercise.');
    }

    /**
     * edit is used to edit exercise data
     *
     * @param  integer $id input parameter
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function edit($id)
    {
        $data = Exercise::with(['interests','equipments','bodyParts','ageGroups','fitnessLevels'])
        ->where('id',$id)->first();
        $exerciseData = $this->exerciseService->getExerciseData();
        return view('business.exercise.manage', compact('data', 'exerciseData'));
    }

    /**
     * update is used to update exercise data
     *
     * @param  ExerciseRequest $request request parameter
     * @return object \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(ExerciseRequest $request)
    {
        try {
            $isUpdated = $this->exerciseService->updateExercise($request);
            if ($isUpdated) {
                Alert::success('Success', 'Exercise has been updated!.', 'success');
                return redirect('business/get-fit/exercises');
            }
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage(), 'error');
        }
        return redirect()->back()->with('error', 'Unable to update exercise.');
    }

    /**
     * viewExercise is used to view exercise details
     *
     * @param  Request $request request parameter
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function viewExercise(Request $request)
    {
        $id = $request->object_id;
        $row = Exercise::with([
            'workoutType:id,name',
            'duration:id,duration',
            'equipments:id,name',
            'bodyParts:id,name',
            'fitnessLevels:id,name',
            'ageGroups:id,name',
            'user:id,user_name,role'
            ])->findOrFail($id);


        $equipments = array();
        if(!empty($row->equipments)){
            foreach($row->equipments as $equipmentVal){
                $equipments[] = '<label class="label label-success mb-2">'.$equipmentVal->name.'</label>';
            }
        }
        $row['equipments'] = implode(' ',$equipments);

        $bodyParts = array();
        if(!empty($row->bodyParts)){
            foreach($row->bodyParts as $bodyPartsVal){
                $bodyParts[] = '<label class="label label-success mb-2">'.$bodyPartsVal->name.'</label>';
            }
        }
        $row['body_parts'] = implode(' ',$bodyParts);

        $fitnessLevelsArr = array();
        if(!empty($row->fitnessLevels)){
            foreach($row->fitnessLevels as $fitnessLevelVal){
                $fitnessLevelsArr[] = '<label class="label label-success mb-2">'.$fitnessLevelVal->name.'</label>';
            }
        }
        $row['fitness_levels'] = implode(' ',$fitnessLevelsArr);

        $ageGroupArr = array();
        if(!empty($row->ageGroups)){
            foreach($row->ageGroups as $ageGroupVal){
                $ageGroupArr[] = '<label class="label label-success mb-2">'.$ageGroupVal->name.'</label>';
            }
        }
        $row['age_group'] = implode(' ',$ageGroupArr);

        $url = Storage::disk($this->fileSystemCloud)->url($this->exerciseOriginalImagePath.$row->poster_image) ?? asset('backend/assets/images/no-icon.png');
        $row['poster_image'] = $url;

        $videoUrl = Storage::disk($this->fileSystemCloud)->url($this->exerciseThumbImagePath.$row->video_thumb) ?? asset('backend/assets/images/no-icon.png');
        $row['exercise_video'] = $videoUrl;

        return view('business.exercise.view',compact('row'));
    }

    /**
     * removeExercise is used to delete exercise data
     *
     * @param  integer $id exercise primary id
     * @return object  \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function removeExercise($id)
    {
        try {
            if (empty($id)) {
                throw new \Exception('Invalid exercise id.');
            }
            $isDeleted = $this->exerciseService->deleteExercise($id);
            if ($isDeleted) {
                Alert::success('Success', 'Exercise has been deleted!.', 'success');
            }
        } catch (\Exception $e) {
            Alert::error('Error', $e->getMessage(), 'error');
        }
        return redirect('business/get-fit/exercises');
    }
}