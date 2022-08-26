<?php

namespace App\Http\Controllers\Web\SuperAdmin;;

use App\Models\PlanGoals;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\PlanGoalRequest;
use App\Services\PlanGoalService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class PlanGoalsController extends Controller
{
    /**
     * $workoutService workout type container
     *
     * @var object
     */
    public $goalService;

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->goalThumbImagePath = Config::get('constant.PLAN_GOAL_THUMB_PHOTO_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->goalService = new PlanGoalService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function index(Request $request)
    {
        $data['page_title'] = 'Plan Goals';
        if ($request->ajax()) {
            $this->goalService->getPlanGoals($request);
        }
        return view('superadmin.get_fit.plan_goal.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function create()
    {
        $data = [];
        return view('superadmin.get_fit.plan_goal.manage', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function store(PlanGoalRequest $request)
    {
        try {
            $result = $this->goalService->crateGoal($request);
            if (!empty($result['status'])) {
                Alert::success('Success', 'Plan goal has been added!.', 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/plan-goal');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PlanGoals  $planGoals
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function show(Request $request)
    {
        $data = PlanGoals::with(array('user'=> function($query) {
                $query->select('id','user_name','role');
            }))->findOrFail($request->object_id);
        $url = Storage::disk($this->fileSystemCloud)->url($this->goalThumbImagePath.$data->icon_file) ?? asset('backend/assets/images/no-icon.png');
        $data['icon_file'] = $url;
        return view('superadmin.get_fit.plan_goal.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PlanGoals  $planGoals
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function edit(Request $request)
    {
        $data = PlanGoals::findOrFail($request->id);
        $data['icon_file'] = !empty($data['icon_file']) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($this->goalThumbImagePath.$data['icon_file']) : asset('backend/assets/images/no-icon.png');
        return view('superadmin.get_fit.plan_goal.manage',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PlanGoals  $planGoals
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(PlanGoalRequest $request)
    {
        try {
            $result = $this->goalService->updateGoal($request);
            if (!empty($result['status'])) {
                Alert::success('Success', $result['message'], 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/plan-goal');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanGoals  $planGoals
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function destroy($id)
    {
        try {
            $planGoal = PlanGoals::findOrFail($id);
            if (empty($planGoal)) {
                Alert::error('Error','Plan goal not found!', 'error');
                return redirect('superadmin/get-fit/plan-goal');
            }
            $isDeleted = false;
            $this->goalService->deleteImageAndThumb($planGoal->icon_file ?? null);
            $isDeleted = $planGoal->delete();
            if ($isDeleted) {
                Alert::success('Success', 'Plan goal has been deleted!.', 'success');
            } else {
                Alert::error('Error', 'Unable to delete plan goal!.', 'error');
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/plan-goal');
    }
}