<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Interests;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class InterestsController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->interestOriginalImagePath = Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->interestThumbImagePath = Config::get('constant.INTEREST_THUMB_PHOTO_UPLOAD_PATH');
        $this->interestThumbImageHeight = Config::get('constant.INTEREST_THUMB_PHOTO_HEIGHT');
        $this->interestThumbImageWidth = Config::get('constant.INTEREST_THUMB_PHOTO_WIDTH');
    }

	/*
		@Author : Spec Developer
		@Desc   : Fetch interest listing.
		@Output : \Illuminate\Http\Response
		@Date   : 21/02/2022
	*/

    public function index(Request $request){
    	$data['page_title'] = 'Interests';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/interests.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css'
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'Interests.init();'
        );

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

            $totalRecords = Interests::select('count(*) as allcount')->count();
            $interestObj = Interests::select('id','interest_name','icon_file','created_at','status')
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where('interest_name', 'like', '%'.$searchValue.'%');
            });

            $totalFilteredRows = $interestObj->count();
            $interestData = $interestObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();
            $data_arr = [];
            $data_arr = $interestData;
            foreach($interestData as $key => $row) {
                $iconUrl = !empty($row->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$row->icon_file) : asset('backend/assets/images/no-icon.png');
                $data_arr[$key]->icon_file = "<img src='".$iconUrl."' width='70px' class='img-radius img-fluid wid-100'>";
                $data_arr[$key]->status = ($row->status == 1) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/interests/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/interests/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/interests/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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

        return view('superadmin.interests.interests_list',$data);
    }

    /*
		@Author : Spec Developer
		@Desc   : Add interest.
		@Output : \Illuminate\Http\Response
		@Date   : 21/02/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Interest';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/interests.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(

        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'Interests.add();'
        );
        $data['noIcon'] = asset('backend/assets/images/no-icon.png');

        return view('superadmin.interests.add_interests',$data);
	}

    /*
		@Author : Spec Developer
		@Desc   : Store new interest data.
		@Output : \Illuminate\Http\Response
		@Date   : 21/02/2022
	*/
    public function store(Request $request){

       try {

            $imgValidationArr['interest_icon'] = 'required|mimes:jpeg,png|max:1024|dimensions:min_width=100,max_width=800';

            $textValidationArr = array(
                'interest_name'	=>	'required|unique:interests,interest_name',
            );
            $validationArr = array_merge($textValidationArr,$imgValidationArr);


            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Add interest by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $interest_icon = null;

            // Upload interest icon
            if (!empty($request->file('interest_icon')) && $request->file('interest_icon')->isValid()) {
                $params = [
                        'originalPath' => $this->interestOriginalImagePath,
                        'thumbPath' => $this->interestThumbImagePath,
                        'thumbHeight' => $this->interestThumbImageHeight,
                        'thumbWidth' => $this->interestThumbImageWidth,
                        'previousImage' => ''
                    ];

                $icon = ImageUpload::uploadWithThumbImage($request->file('interest_icon'), $params);
                if ($icon === false) {
                    DB::rollback();
                    //return errorResponse(trans('log-messages.USER_IMAGE_UPLOAD_ERROR_MESSAGE'),STATUS_CODE_ERROR);
                    return redirect()->back()->withErrors(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'))->withInput();
                }
                $interest_icon = $icon['imageName'];
            }


            $interestObj = new Interests([
                'interest_name' => trim($request->get('interest_name')),
                'status'        =>$request->get('status'),
                'icon_file'     => $interest_icon,
            ]);
            $interestObj->save();
            //$id = $interestObj->id;

            Alert::success('Success', 'Interest has been added!.', 'success');
		    return redirect('superadmin/interests');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/interests/add');
        }
    }

    /*
        @Author : Spec Developer
        @Desc   : Check interests name is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 21/02/2022
    */

    public function checkInterstExists(Request $request){

        $interestId = $request->id;
        if(!empty($interestId)){
            $result = Interests::where('interest_name', $request->interest_name)->whereNotIn('id', [$interestId])->count();
        }
        else
        {
            $result = Interests::where('interest_name', $request->interest_name)->count();
        }

        if($result == 0){
            $return =  true;
        }
        else{
            $return= false;
        }
        echo json_encode($return);
        exit;
    }

    /*
		@Author : Spec Developer
		@Desc   : Edit interest.
		@Output : \Illuminate\Http\Response
		@Date   : 21/02/2022
	*/

    public function editInterest($id){

        $data['page_title'] = 'Edit Interest';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/interests.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(

        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'Interests.edit();'
        );
        $results	=   Interests::where('id',$id)->first();
        $data['old_icon'] = $results->icon_file;
        $results['icon_file'] = !empty($results->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$results->icon_file) : asset('backend/assets/images/no-icon.png');
        $results['placeholder_url'] = asset('backend/assets/images/no-icon.png');

        $data['data'] = $results;
        return view('superadmin.interests.edit_interests',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Update interest details.
        @Output : \Illuminate\Http\Response
        @Date   : 22/02/2022
    */

	public function updateInterest(Request $request){

        try {
            $id = $request->input('interest_id');

            $imgValidationArr = array();

            if(empty($request->input('old_icon'))){
                $imgValidationArr['interest_icon'] = 'required|mimes:jpeg,png|max:1024|dimensions:min_width=100,max_width=800';
            }

            $textValidationArr = array(
                'interest_name'	=>	'required|unique:interests,interest_name,'.$id,
            );
            $validationArr = array_merge($textValidationArr,$imgValidationArr);


            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Edit interest by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Upload User Photo
            if (!empty($request->file('interest_icon')) && $request->file('interest_icon')->isValid()) {
                $params = [
                        'originalPath'  => $this->interestOriginalImagePath,
                        'thumbPath'     => $this->interestThumbImagePath,
                        'thumbHeight'   => $this->interestThumbImageHeight,
                        'thumbWidth'    => $this->interestThumbImageWidth,
                        'previousImage' => $request->old_icon,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('interest_icon'), $params);
                if ($userPhoto === false) {
                    DB::rollback();
                    Alert::error('Error', trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE') , 'error');
                }

                $old_image = Interests::select('icon_file')->where('id',$id)->first();
                if(!empty($old_image->icon_file)){
                    $image_original_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$old_image->icon_file);
                    $image_thumb_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.INTEREST_THUMB_PHOTO_UPLOAD_PATH').$old_image->icon_file);

                    if (file_exists($image_original_path)) {
                        @unlink($image_original_path);
                    }
                    if (file_exists($image_thumb_path)) {
                        @unlink($image_thumb_path);
                    }
                }

                $input['icon_file'] = $userPhoto['imageName'];
            }

            $input['interest_name'] = trim($request->interest_name);
            $input['status']        = $request->status;
            //dd($input);
            Interests::where('id', $id)->update($input);

            Alert::success('Success', 'Interest details updated.', 'success');
		    return redirect('superadmin/interests');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/interests');
        }
	}

    /*
        @Author : Spec Developer
        @Desc   : View interest details.
        @Date   : 22/02/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;
		$row['data']	=	Interests::find($id);

        $iconUrl = !empty($row['data']->icon_file) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$row['data']->icon_file) : asset('backend/assets/images/no-icon.png');

		$row['data']['icon_file'] = $iconUrl;

        return view('superadmin.interests.view_interests',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete interest record.
        @Output : \Illuminate\Http\Response
        @Date   : 22/02/2022
    */

    public function destroy($id){
		$obj = Interests::find($id);

        $image_original_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.INTEREST_ORIGINAL_PHOTO_UPLOAD_PATH').$obj['icon_file']);
        $image_thumb_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.INTEREST_THUMB_PHOTO_UPLOAD_PATH').$obj['icon_file']);

		if (file_exists($image_original_path)) {
			@unlink($image_original_path);
		}
        if (file_exists($image_thumb_path)) {
			@unlink($image_thumb_path);
		}
		$obj->delete();
        Alert::success('Success', 'Interest has been deleted successfully!', 'success');
		return redirect('superadmin/interests');
    }


}