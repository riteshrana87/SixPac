<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Models\PlanSport;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\PlanSportRequest;
use App\Services\PlanSportService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class PlanSportController extends Controller
{

    /**
     * $sportService sport service container
     *
     * @var object
     */
    public $sportService;

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->sportThumbImagePath = Config::get('constant.PLAN_SPORT_THUMB_PHOTO_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->sportService = new PlanSportService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function index(Request $request)
    {
        $data['page_title'] = 'Plan Sports';
        if ($request->ajax()) {
            $this->sportService->getPlanSports($request);
        }
        return view('superadmin.get_fit.plan_sport.list', $data);
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
        return view('superadmin.get_fit.plan_sport.manage', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function store(PlanSportRequest $request)
    {
        try {
            $result = $this->sportService->cratePlanSport($request);
            if (!empty($result['status'])) {
                Alert::success('Success', 'Plan sport has been added!.', 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/plan-sport');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PlanSport  $planSport
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function show(Request $request)
    {
        $data = PlanSport::with(array('user'=> function($query) {
                $query->select('id','user_name','role');
            }))->findOrFail($request->object_id);
        $url = Storage::disk($this->fileSystemCloud)->url($this->sportThumbImagePath.$data->icon_file) ?? asset('backend/assets/images/no-icon.png');
        $data['icon_file'] = $url;
        return view('superadmin.get_fit.plan_sport.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PlanSport  $planSport
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function edit(Request $request)
    {
        $data = PlanSport::findOrFail($request->id);
        $data['icon_file'] = !empty($data['icon_file']) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($this->sportThumbImagePath.$data['icon_file']) : asset('backend/assets/images/no-icon.png');
        return view('superadmin.get_fit.plan_sport.manage',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PlanSport  $planSport
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(PlanSportRequest $request)
    {
         try {
            $result = $this->sportService->updatePlanSport($request);
            if (!empty($result['status'])) {
                Alert::success('Success', $result['message'], 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/plan-sport');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanSport  $planSport
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function destroy($id)
    {
        try {
            $isDeleted = $this->sportService->removePlanSport($id);
            if ($isDeleted) {
                Alert::success('Success', 'Plan sport has been deleted!.', 'success');
            } else {
                Alert::error('Error', 'Unable to delete plan sport!.', 'error');
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/plan-sport');
    }
}
