<?php
namespace App\Http\Controllers\Web\Business;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\Controller;
use App\Services\ExerciseService;
use App\Services\WorkoutPlanService;
use App\Models\WorkoutProgram;
use App\Models\Exercise;

use Illuminate\Http\Request;

class WorkoutPlanController extends Controller
{
    /**
     * $equipmentService workout type container
     *
     * @var object
     */

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->planThumbImagePath = Config::get('constant.WORKOUT_PLAN_THUMB_PHOTO_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->planService = new WorkoutPlanService();
        $this->exerciseService = new ExerciseService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $this->planService->getWorkoutPlan($request);
        }
        return view('business.workout_plan.list',[]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function create()
    {
        $workoutPlanData = $this->exerciseService->getExerciseData();
        $data = [];
        return view('business.workout_plan.manage', compact('data', 'workoutPlanData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\equipment  $equipment
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function show(Request $request)
    {
        $id = $request->object_id;
        $row = WorkoutProgram::with(['getPlanDay:id,name','user:id,user_name,role'])->findOrFail($id);
        $url = Storage::disk($this->fileSystemCloud)->url($this->planThumbImagePath.$row->poster_image) ?? asset('backend/assets/images/no-icon.png');
        $row['poster_image'] = $url;
        return view('business.workout_plan.view',compact('row'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\equipment  $equipment
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function edit(Request $request)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\equipment  $equipment
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(Request $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\equipment  $equipment
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function destroy($id)
    {

    }
}