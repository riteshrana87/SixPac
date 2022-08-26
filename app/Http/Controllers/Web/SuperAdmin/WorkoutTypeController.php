<?php
namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\WorkoutType;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\WorkoutTypeRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Config;
use DataTables;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Storage;
use App\Services\WorkoutTypeService;

class WorkoutTypeController extends Controller
{
    /**
     * $workoutService workout type container
     *
     * @var object
     */
    public $workoutService;

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->workoutOriginalImagePath = Config::get('constant.WORKOUT_TYPE_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImagePath = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_UPLOAD_PATH');
        $this->workoutThumbImageHeight = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_HEIGHT');
        $this->workoutThumbImageWidth = Config::get('constant.WORKOUT_TYPE_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->workoutService = new WorkoutTypeService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function index(Request $request)
    {
        $data['page_title'] = 'Workout Types';

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

            $totalRecords = WorkoutType::with(array('usersData'=> function($query) {
                $query->select('id','user_name','role');
            },
            'getFitData' => function ($query) {
                $query->select('id', 'name');
            }))->select('count(*) as allcount')->count();

            $workoutObj = WorkoutType::with(array('usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->where('name', 'like', '%'.$searchValue.'%')
                    ->OrWhere(function($query2) use ($searchValue) {
                        $query2->whereHas('usersData.business', function ($query3) use ($searchValue) {
                            $query3->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                        })->OrWhereHas('usersData', function ($query4) use ($searchValue) {
                            $query4->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                        });
                    });
                });
            });

            $totalFilteredRows = $workoutObj->count();
            $workoutTypeData = $workoutObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();

            $data_arr = [];
            $data_arr = $workoutTypeData;
            foreach($workoutTypeData as $key => $row) {
                $iconUrl = !empty($row->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($this->workoutThumbImagePath.$row->icon_file) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' width='70px' class='img-radius img-fluid wid-100'>";
                if ($row->usersData) {
                    $data_arr[$key]->created_by = (($row->usersData->role==3) ? $row->usersData->business->company_name : $row->usersData->user_name) ?? '-';
                } else {
                    $data_arr[$key]->created_by = '-';
                }
                $data_arr[$key]->status = ($row->status == 1) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/workout-type/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/get-fit/workout-type/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/workout-type/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
                $data_arr[$key]->action = $btn;
            }
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
             );
            echo json_encode($response); exit;

        }
        return view('superadmin.get_fit.workout_type.list', $data);
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
        // $getFitData = $this->workoutService->getFitList();
        return view('superadmin.get_fit.workout_type.manage', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function store(WorkoutTypeRequest $request)
    {
        try {
            $result = $this->workoutService->storeType($request);
            if (!empty($result['status'])) {
                Alert::success('Success', 'Workout type has been added!.', 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/workout-type');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkoutType  $workoutType
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function show(Request $request)
    {
        $data = workoutType::findOrFail($request->object_id);
        $url = Storage::disk($this->fileSystemCloud)->url($this->workoutThumbImagePath.$data->icon_file) ?? asset('backend/assets/images/no-icon.png');
        $data['icon_file'] = $url;
        return view('superadmin.get_fit.workout_type.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkoutType  $workoutType
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function edit(Request $request)
    {
        $data = workoutType::findOrFail($request->id);
        $data['icon_file'] = !empty($data['icon_file']) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($this->workoutThumbImagePath.$data['icon_file']) : asset('backend/assets/images/no-icon.png');
        // $getFitData = $this->workoutService->getFitList();
        return view('superadmin.get_fit.workout_type.manage',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WorkoutType  $workoutType
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(WorkoutTypeRequest $request)
    {
        try {
            $result = $this->workoutService->updateType($request);
            if (!empty($result['status'])) {
                Alert::success('Success', $result['message'], 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/workout-type');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkoutType  $workoutType
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function destroy($id)
    {
        try {
            $workoutType = workoutType::findOrFail($id);
            if (empty($workoutType)) {
                Alert::error('Error','Workout type not found!', 'error');
                return redirect('superadmin/get-fit/workout-type');
            }
            $isDeleted = false;
            $this->workoutService->deleteImageAndThumb($workoutType->icon_file ?? null);
            $isDeleted = $workoutType->delete();
            if ($isDeleted) {
                Alert::success('Success', 'Workout type has been deleted!.', 'success');
            } else {
                Alert::error('Error', 'Unable to delete workout type!.', 'error');
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/workout-type');
    }
}