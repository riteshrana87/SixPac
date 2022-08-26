<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\PlanGoals;

class PlanGoalService
{

    public function __construct()
    {
        $this->goalOriginalImagePath = Config::get('constant.PLAN_GOAL_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->goalThumbImagePath = Config::get('constant.PLAN_GOAL_THUMB_PHOTO_UPLOAD_PATH');
        $this->goalThumbImageHeight = Config::get('constant.PLAN_GOAL_THUMB_PHOTO_HEIGHT');
        $this->goalThumbImageWidth = Config::get('constant.PLAN_GOAL_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
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
            if ($request->hasFile('icon_file') && $request->file('icon_file')->isValid()) {
                $file = $request->file('icon_file');
                $params = [
                    'originalPath' => $this->goalOriginalImagePath,
                    'thumbPath' => $this->goalThumbImagePath,
                    'thumbHeight' => $this->goalThumbImageHeight,
                    'thumbWidth' => $this->goalThumbImageWidth,
                    'previousImage' => ''
                ];
                return ImageUpload::uploadWithThumbImage($request->file('icon_file'), $params);
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
            $originalPath = $this->goalOriginalImagePath.$imageName;
            $thumbPath = $this->goalThumbImagePath.$imageName;
            $isImageDeleted = Storage::disk($this->fileSystemCloud)->exists($originalPath) ? Storage::disk($this->fileSystemCloud)->delete($originalPath) : false;
            $isThumbDeleted = Storage::disk($this->fileSystemCloud)->exists($thumbPath) ? Storage::disk($this->fileSystemCloud)->delete($thumbPath) : false;
        }
        return ($isImageDeleted && $isThumbDeleted) ? true : false;
    }

    /**
     * getPlanGoals is used to get plan goals
     *
     * @param  Request $request request parameter
     * @return array   result array
     * @author Spec Developer
     */
    public function getPlanGoals(Request $request) {
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

            $totalRecords = PlanGoals::with(array('user'=> function($query) {
                $query->select('id','user_name','role');
            },
            'getFitData' => function ($query) {
                $query->select('id', 'name');
            }))->select('count(*) as allcount')->count();
            $goalObj = PlanGoals::with(array('user'=> function($query) {
                $query->select('id','user_name','role');
            }))
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->where('name', 'like', '%'.$searchValue.'%')
                    ->OrWhere(function($query2) use ($searchValue) {
                        $query2->whereHas('user.business', function ($query3) use ($searchValue) {
                            $query3->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                        })->OrWhereHas('user', function ($query4) use ($searchValue) {
                            $query4->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                        });
                    });
                });
            });

            $totalFilteredRows = $goalObj->count();
            $planGoalData = $goalObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();

            $data_arr = [];
            $data_arr = $planGoalData;
            foreach($planGoalData as $key => $row) {
                $iconUrl = !empty($row->icon_file) ? Storage::disk($this->fileSystemCloud)->url($this->goalThumbImagePath.$row->icon_file) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' width='70px' class='img-radius img-fluid wid-100'>";
                if ($row->user) {
                    $data_arr[$key]->created_by = (($row->user->role==3) ? $row->user->business->company_name : $row->user->user_name) ?? '-';
                } else {
                    $data_arr[$key]->created_by = '-';
                }
                $data_arr[$key]->status = ($row->status == 1) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/plan-goal/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/get-fit/plan-goal/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/plan-goal/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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
     * storeType is used to store plan goal data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function crateGoal(Request $request): ?array
    {
        try {
            $input = $request->all();
            if ($request->hasFile('icon_file')) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['icon_file'] = $userPhoto['imageName'];
            }
            $workoutType = PlanGoals::create($input);
            return ['status' => true,'message'=>'','data'=>$workoutType];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }
    }

    /**
     * updateType is used to update plan goal data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function updateGoal(Request $request): ?array
    {
        try {
            $input = $request->all();
            $planGoal = PlanGoals::findOrFail($request->id);
            if ($request->hasFile('icon_file')) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['icon_file'] = $userPhoto['imageName'];
                $this->deleteImageAndThumb($planGoal->icon_file ?? null);
            }
            $planGoal->fill($input);
            $status = $planGoal->save() ? true : false;
            return ['status' => $status,'message'=> $status ? 'Plan goal has been updated!.' : 'Unable to update plan goal!.','data'=>$planGoal];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }
    }
}