<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageUpload;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\GetFit;
use App\Models\Equipment;

class EquipmentService
{

    public function __construct()
    {
        $this->equipmentOriginalImagePath = Config::get('constant.EQUIPMENT_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->equipmentThumbImagePath = Config::get('constant.EQUIPMENT_THUMB_PHOTO_UPLOAD_PATH');
        $this->equipmentThumbImageHeight = Config::get('constant.EQUIPMENT_THUMB_PHOTO_HEIGHT');
        $this->equipmentThumbImageWidth = Config::get('constant.EQUIPMENT_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
    }

    /**
     * uploadImage is used to upload equipment icon image
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
                    'originalPath' => $this->equipmentOriginalImagePath,
                    'thumbPath' => $this->equipmentThumbImagePath,
                    'thumbHeight' => $this->equipmentThumbImageHeight,
                    'thumbWidth' => $this->equipmentThumbImageWidth,
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
            $originalPath = $this->equipmentOriginalImagePath.$imageName;
            $thumbPath = $this->equipmentThumbImagePath.$imageName;
            $isImageDeleted = Storage::disk($this->fileSystemCloud)->exists($originalPath) ? Storage::disk($this->fileSystemCloud)->delete($originalPath) : false;
            $isThumbDeleted = Storage::disk($this->fileSystemCloud)->exists($thumbPath) ? Storage::disk($this->fileSystemCloud)->delete($thumbPath) : false;
        }
        return ($isImageDeleted && $isThumbDeleted) ? true : false;
    }

    /**
     * storeType is used to store equipment data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function storeType(Request $request): ?array
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
            $equipment = Equipment::create($input);
            return ['status' => true,'message'=>'','data'=>$equipment];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }
    }

    /**
     * updateType is used to update equipment data
     *
     * @param  Request $request input parameter
     * @return array   response array
     * @author Spec Developer
     */
    public function updateType(Request $request): ?array
    {
        try {
            $input = $request->all();
            $equipment = Equipment::findOrFail($request->id);
            if ($request->hasFile('icon_file')) {
                $userPhoto = $this->uploadImage($request);
                if ($userPhoto === false) {
                    throw new \Exception(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'));
                }
                $input['icon_file'] = $userPhoto['imageName'];
                $this->deleteImageAndThumb($equipment->icon_file ?? null);
            }
            $equipment->fill($input);
            $status = $equipment->save() ? true : false;
            return ['status' => $status,'message'=> $status ? 'Equipment has been updated!.' : 'Unable to update equipment!.','data'=>$equipment];
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return ['status' => false,'message'=>$e->getMessage(),'data'=>array()];
        }
    }

    /**
     * getFitList is used to get fit list
     *
     * @return array getfit list array
     * @author Spec Developer
     */
    // public function getFitList(): ?array
    // {
    //     return GetFit::where('status', 1)->pluck('name', 'id')->toArray();
    // }

    public function getFitList(Request $request) {
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

            $totalRecords = Equipment::with(array('user'=> function($query) {
                $query->select('id','user_name','role');
            },
            'getFitData' => function ($query) {
                $query->select('id', 'name');
            }))->select('count(*) as allcount')->count();
            $equipmentObj = Equipment::with(array('user'=> function($query) {
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

            $totalFilteredRows = $equipmentObj->count();
            $equipmentData = $equipmentObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();

            $data_arr = [];
            $data_arr = $equipmentData;
            foreach($equipmentData as $key => $row) {
                $iconUrl = !empty($row->icon_file) ? Storage::disk($this->fileSystemCloud)->url($this->equipmentThumbImagePath.$row->icon_file) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' width='70px' class='img-radius img-fluid wid-100'>";
                if ($row->user) {
                    $data_arr[$key]->created_by = (($row->user->role==3) ? $row->user->business->company_name : $row->user->user_name) ?? '-';
                } else {
                    $data_arr[$key]->created_by = '-';
                }
                $data_arr[$key]->status = ($row->status == 1) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/equipment/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/get-fit/equipment/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/get-fit/equipment/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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