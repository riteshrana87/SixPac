<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\GetFit;
use App\Models\WorkoutProgram;

class WorkoutPlanService
{

    public function __construct()
    {
        $this->planOriginalImagePath = Config::get('constant.WORKOUT_PLAN_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->planThumbImagePath = Config::get('constant.WORKOUT_PLAN_THUMB_PHOTO_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
    }

    /**
     * getFitList is used to get fit list
     *
     * @return array getfit list array
     * @author Spec Developer
     */
    public function getWorkoutPlan(Request $request): ?array
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
            $totalRecords = WorkoutProgram::select('count(*) as allcount')->count();
            $exerciseObj = WorkoutProgram::with('getPlanDay:id,name')
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->where('name', 'like', '%'.$searchValue.'%')                    
                    ->OrWhereHas('getPlanDay', function ($query2) use ($searchValue) {
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
                 $iconUrl = !empty($row->poster_image) ? Storage::disk($this->fileSystemCloud)->url($this->planThumbImagePath.$row->poster_image) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' width='70px' class='img-radius img-fluid wid-100'>";
                if ($row->getPlanDay) {
                     $data_arr[$key]->plan_day = $row->getPlanDay->name ?? '-';
                }
                $data_arr[$key]->status = !empty($row->status) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/get-fit/workout-plan/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('business/get-fit/workout-plan/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/get-fit/workout-plan/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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
}