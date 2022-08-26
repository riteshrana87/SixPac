<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Models\OnDemandService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DemandServices;
use App\Http\Requests\SuperAdmin\OnDemandRequest;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\Log;

class OnDemandServiceController extends Controller
{
    /**
     * $demandService demand service object container
     *
     * @var object
     */
    public $demandService;

    public function __construct()
    {
        $this->demandService = new DemandServices();
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
            $servicesData = OnDemandService::with(array('usersData'=> function($query) {
                $query->select('id','user_name','role');
            },
            'getFitData' => function ($query) {
                $query->select('id', 'name');
            }))
            ->orderByDesc('id')
            ->get();
            return Datatables::of($servicesData)
                ->addColumn('id', function($row){
                    return $row->id;
                })
                ->addColumn('service', function($row){
                    return $row->service;
                })
                ->addColumn('getfit_category', function($row){
                    return $row->getFitData->name;
                })
                ->addColumn('user_id', function($row){
                    return (($row->usersData->role==3) ? $row->usersData->business->company_name : $row->usersData->user_name) ?? '-';
                })
                ->addColumn('created_at', function($row){
                    return $row->created_at;
                })
                ->addColumn('status', function($row){
                    $status = '<label class="label label-success">Active</label>';
                    if($row->status == 0){
                        $status = '<label class="label label-danger">Deactive</label>';
                    }
                    return $status;
                })
                ->addColumn('action', function($row){
                    $btn = '';
                    $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/on-demand-services/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                    $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/get-fit/on-demand-services/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                    $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/on-demand-services/destroy').'/'.$row->id.'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';

                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
        }
         return view('superadmin.get_fit.on_demand_services.list');
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
        $getDemandData = $this->demandService->getFitList();
        return view('superadmin.get_fit.on_demand_services.manage', compact('data','getDemandData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OnDemandRequest $request)
    {
        try {
            $result = $this->demandService->storeService($request);          
            if (!empty($result['status'])) {
                Alert::success('Success', 'Service has been added!.', 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {           
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/on-demand-services');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OnDemandService  $onDemandService
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function show(Request $request)
    {
        $data = OnDemandService::with(['getFitData' => function ($query) {
                $query->select('id', 'name');
            }])->findOrFail($request->object_id);
        return view('superadmin.get_fit.on_demand_services.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OnDemandService  $onDemandService
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function edit(Request $request)
    {
        $data = OnDemandService::findOrFail($request->id);
        $getDemandData = $this->demandService->getFitList();
        return view('superadmin.get_fit.on_demand_services.manage',compact('data', 'getDemandData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OnDemandService  $onDemandService
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(OnDemandRequest $request)
    {
        try {
            $result = $this->demandService->updateService($request);
            if (!empty($result['status'])) {
                Alert::success('Success', $result['message'], 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/on-demand-services');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OnDemandService  $onDemandService
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function destroy($id)
    {
        try {
            $serviceData = OnDemandService::findOrFail($id);
            if (empty($serviceData)) {
                Alert::error('Error','Service not found!', 'error');
                return redirect('superadmin/get-fit/on-demand-services');
            }
            $isDeleted = $serviceData->delete();
            if ($isDeleted) {
                Alert::success('Success', 'Service has been deleted!.', 'success');
            } else {
                Alert::error('Error', 'Unable to delete service!.', 'error');
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/on-demand-services');
    }
}
