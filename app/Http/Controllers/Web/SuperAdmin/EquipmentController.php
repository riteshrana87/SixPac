<?php
namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use Illuminate\Http\Request;
use App\Http\Requests\SuperAdmin\EquipmentRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Config;
use DataTables;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Storage;
use App\Services\EquipmentService;

class EquipmentController extends Controller
{
    /**
     * $equipmentService workout type container
     *
     * @var object
     */
    public $equipmentService;

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->equipmentOriginalImagePath = Config::get('constant.EQUIPMENT_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->equipmentThumbImagePath = Config::get('constant.EQUIPMENT_THUMB_PHOTO_UPLOAD_PATH');
        $this->equipmentThumbImageHeight = Config::get('constant.EQUIPMENT_THUMB_PHOTO_HEIGHT');
        $this->equipmentThumbImageWidth = Config::get('constant.EQUIPMENT_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->equipmentService = new EquipmentService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function index(Request $request)
    {
        $data['page_title'] = 'Equipments';
        if ($request->ajax()) {
            $this->equipmentService->getFitList($request);
        }
        return view('superadmin.get_fit.equipment.list', $data);
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
        // $getFitData = $this->equipmentService->getFitList();
        return view('superadmin.get_fit.equipment.manage', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function store(equipmentRequest $request)
    {
        try {
            $result = $this->equipmentService->storeType($request);
            if (!empty($result['status'])) {
                Alert::success('Success', 'Equipment has been added!.', 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/equipment');
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
        $data = Equipment::findOrFail($request->object_id);
        $url = Storage::disk($this->fileSystemCloud)->url($this->equipmentThumbImagePath.$data->icon_file) ?? asset('backend/assets/images/no-icon.png');
        $data['icon_file'] = $url;
        return view('superadmin.get_fit.equipment.view',compact('data'));
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
        $data = Equipment::findOrFail($request->id);
        $data['icon_file'] = !empty($data['icon_file']) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url($this->equipmentThumbImagePath.$data['icon_file']) : asset('backend/assets/images/no-icon.png');
        // $getFitData = $this->equipmentService->getFitList();
        return view('superadmin.get_fit.equipment.manage',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\equipment  $equipment
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function update(equipmentRequest $request)
    {
        try {
            $result = $this->equipmentService->updateType($request);
            if (!empty($result['status'])) {
                Alert::success('Success', $result['message'], 'success');
            } else {
                Alert::error('Error', $result['message'], 'error');
            }
        } catch (\Exception $e) {
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/equipment');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Equipment  $Equipment
     * @return \Illuminate\Http\Response
     * @author Spec Developer
     */
    public function destroy($id)
    {
        try {
            $equipmentObj = Equipment::findOrFail($id);
            if (empty($equipmentObj)) {
                Alert::error('Error','Equipment not found!', 'error');
                return redirect('superadmin/get-fit/equipment');
            }
            $isDeleted = false;
            $this->equipmentService->deleteImageAndThumb($equipmentObj->icon_file ?? null);
            $isDeleted = $equipmentObj->delete();
            if ($isDeleted) {
                Alert::success('Success', 'Equipment has been deleted!.', 'success');
            } else {
                Alert::error('Error', 'Unable to delete equipment!.', 'error');
            }
        } catch (\Exception $e) {
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
        }
        return redirect('superadmin/get-fit/equipment');
    }
}