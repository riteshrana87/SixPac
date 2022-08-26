<?php

namespace App\Http\Controllers\Web\Business;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ConsumerProfileDetail;
use App\Http\Resources\UserRegisterResource;
use Hash;
use DataTables;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use App\Http\Resources\UsersResource;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\DataTableService;

class EmployeeUserController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->userOriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->userThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->userThumbImageHeight = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->userThumbImageWidth = Config::get('constant.USER_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->dataTable = new DataTableService();
    }
	/*
		@Author : Spec Developer
		@Desc   : Fetch employee user listing
		@Output : \Illuminate\Http\Response
		@Date   : 02/03/2022
	*/
    public function index(Request $request){
    	$data['page_title'] = 'Employee Users';
		$data['page_js'] = array(
            'backend/assets/business/js/employee_users.js'
        );
        $data['extra_css'] = array(
            //'assets/css/scrollspyNav.css',
            'plugins/table/datatable/datatables.css',
			'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
			'plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
			'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(

        );
		$data['extra_js'] = array(
            'plugins/datatables/jquery.dataTables.min.js',
            'plugins/datatables/dataTables.buttons.min.js',
            'plugins/datatables/jszip.min.js',
            'plugins/datatables/buttons.html5.min.js',
            'plugins/datatables/bootstrap.bundle.min.js',
            'plugins/table/datatable/datatables.js',
			'plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
			'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'EmployeeUsers.init();'
        );
        if ($request->ajax()) {
            $select = ['id','name','email','phone','created_at','status'];
            $query = User::whereNotIn('id', [Auth::user()->id])
            ->where(['role' => 4,'created_by' => Auth::user()->id]);
            $search = ['name', 'email', 'phone'];
            $actions = [
                'view' => url('business/users/employee-users/view'),
                'edit' => url('business/users/employee-users/edit'),
                'delete' => url('business/users/employee-users/destroy/')
            ];
            $this->dataTable->showTable($request,$query,$select, $search, $actions, 1);
        }
        return view('business.users.employee_user_list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add employee user.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Employee User';
        $data['page_js']    = array(
            'backend/assets/business/js/employee_users.js'
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
            'EmployeeUsers.add();'
        );

        // Get all country
        // $data['country'] = Country::orderBy('name','asc')->get(); // For all country
        // $data['country'] = Country::where('id',233)->orderBy('name','asc')->get(); // 233 is id of United State (USA)

        $data['countryArr'] = Country::select('countries.id as id','countries.name as name')
			->join('states','states.country_id','=','countries.id')
			->join('cities','cities.state_id','=','states.id')
			->orderby('countries.name','ASC')
			->groupby('cities.country_id')
			->get();

        $data['stateArr'] = Country::select('states.id as id','states.name as name')
			->join('states','states.country_id','=','countries.id')
			->join('cities','cities.state_id','=','states.id')
			->where('states.country_id',233)
			->orderby('states.name','ASC')
			->groupby('states.id')
			->get();


		$data['placeholder_url'] = asset('backend/assets/images/no-avtar.png');
        return view('business.users.add_employee_user',$data);
	}

	/*
		@Author : Spec Developer
		@Desc   : Store new employee user data.
		@Output : \Illuminate\Http\Response
		@Date   : 02/03/2022
	*/
    public function store(Request $request){

       try {

            $imgValidationArr['avtar'] = 'required|mimes:jpeg,png|max:1024|dimensions:min_width=100,max_width=800';

            $textValidationArr = array(
                'name'	=>	'required|min:4|max:40',
                'phone'	=>	'required|unique:users,phone',
                'email'	=>	'required|unique:users,email',
                'password'	=>	'required|min:6|max:15',
                'confirm_password'	=>	'required|min:6|max:15',
                'user_name' => 'required|min:4|max:50|unique:users,user_name'

            );
            $validationArr = array_merge($textValidationArr,$imgValidationArr);


            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Add employee user by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $avtar = null;

            // Upload interest icon
            if (!empty($request->file('avtar')) && $request->file('avtar')->isValid()) {
                $params = [
                        'originalPath' 	=> $this->userOriginalImagePath,
                        'thumbPath' 	=> $this->userThumbImagePath,
                        'thumbHeight' 	=> $this->userThumbImageHeight,
                        'thumbWidth' 	=> $this->userThumbImageWidth,
                        'previousImage' => ''
                    ];
                    $file = $request->file('avtar');
                    if (!empty($request->get('avtarImg'))) {
                        $gallery = $request->get('avtarImg');
                        $base64_str = substr($gallery, strpos($gallery, ",")+1);
                        $decodedFile = base64_decode($base64_str);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = Str::random(20) . '.' . $extension;
                        $params['fileName'] = $fileName;
                        $avtarFile = Storage::disk('public')->put($this->userThumbImagePath.$fileName, $decodedFile);
                        $avtarFile = ImageUpload::uploadMedia($file, $params);
                    } else {
                        $avtarFile = ImageUpload::uploadWithThumbImage($file, $params);
                    }
                if ($avtarFile === false) {
                    DB::rollback();
                    return redirect()->back()->withErrors(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'))->withInput();
                }
                $avtar = $avtarFile['imageName'];
            }

			$phone = "";
			if(!empty($request->get('phone'))){
				$phoneNumber = trim($request->get('phone'));
				$phone = convertPhoneToInt($phoneNumber);
			}

            $objUser = new User([
                'name' 		=> trim($request->get('name')),
                'user_name' => trim($request->get('user_name')),
                'gender' 	=> $request->get('gender'),
                'phone' 	=> $phone,
                'email' 	=> trim($request->get('email')),
                'password' 	=> Hash::make($request->get('password')),
                'status'	=> $request->get('status'),
				'role' 		=> 4,
				'created_by' => Auth::user()->id,
                'avtar'     => $avtar,
            ]);
            $objUser->save();
            $userId = $objUser->id;

            $obj = new ConsumerProfileDetail([
                'user_id'   => $userId,
                'address'	=> trim($request->get('address')),
                'unit_apt'	=> trim($request->get('unit_apt')),
                'city' 		=> trim($request->get('city')),
                'state' 	=> trim($request->get('state')),
                'country' 	=> trim($request->get('country')),
                'zipcode' 	=> trim($request->get('zipcode')),
            ]);
            $obj->save();

            Alert::success('Success', 'Employee user has been added!.', 'success');
		    return redirect('business/users/employee-users');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/users/employee-users/add');
        }
    }

    /*
		@Author : Spec Developer
		@Desc   : Edit employee user.
		@Output : \Illuminate\Http\Response
		@Date   : 02/03/2022
	*/

    public function editEmployeeUser($id){

        $data['page_title'] = 'Edit Employee User';
        $data['page_js']    = array(
            'backend/assets/business/js/employee_users.js'
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
            'EmployeeUsers.edit();'
        );
        //$results	=   User::where('id',$id)->first();
        $user = User::where('id', $id)->with('consumer')->first();
        $results = new UserRegisterResource($user);

        $data['old_avtar'] = $results->avtar;

        $data['address'] = !empty($results->consumer->address) ? $results->consumer->address : NULL;
        $data['unit_apt'] = !empty($results->consumer->unit_apt) ? $results->consumer->unit_apt : NULL;
        $data['city'] = !empty($results->consumer->city) ? $results->consumer->city : NULL;
        $data['state'] = !empty($results->consumer->state) ? $results->consumer->state : NULL;
        $data['country'] = !empty($results->consumer->country) ? $results->consumer->country : NULL;
        $data['zipcode'] =!empty($results->consumer->zipcode) ?  $results->consumer->zipcode : NULL;
        $data['phone'] =!empty($results->phone) ?  convertPhoneToUsFormat($results->phone) : NULL;
		$results['avtar_url'] = !empty($results->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH').$results->avtar) : asset('backend/assets/images/no-avtar.png');
        $results['placeholder_url'] = asset('backend/assets/images/no-avtar.png');
        $data['original_image'] = !empty($results->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$results->avtar) : asset('backend/assets/images/no-avtar.png');
        
        $cityData = City::where('id', $data['city'])
                ->with(['getCityState','getCityState.getStateCountry'])
                ->first();
        $data['city_name'] = !empty($cityData->name) ? $cityData->name : null;
        $data['state_name'] = !empty($cityData->getCityState) ? $cityData->getCityState['name'] : null;
        $data['country_name'] = !empty($cityData->getCityState->getStateCountry) ? $cityData->getCityState->getStateCountry['name'] : null;

        $data['data'] = $results;
        return view('business.users.edit_employee_user',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Update employee user details.
        @Output : \Illuminate\Http\Response
        @Date   : 02/03/2022
    */

	public function updateEmployeeUser(Request $request){

        try {
            $id = $request->input('user_id');

            $imgValidationArr = array();

            if(empty($request->input('old_avtar'))){
                $imgValidationArr['avtar'] = 'required|mimes:jpeg,png|max:1024|dimensions:min_width=100,max_width=800';
            }

            $textValidationArr = array(
                'name'	=>	'required|min:4|max:40',
                'phone'	=>	'required|unique:users,phone,'.$id,
                'email'	=>	'required|unique:users,email,'.$id,
                'user_name' => 'required|min:4|max:50|unique:users,user_name,'.$id
            );
            $validationArr = array_merge($textValidationArr,$imgValidationArr);


            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Edit employee user by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (empty($request->file('avtar')) && !empty($request->get('avtarImg'))) {           
               $gallery = $request->get('avtarImg');
               $base64_str = substr($gallery, strpos($gallery, ",")+1);
               $file = base64_decode($base64_str);                
                $gallery = User::find($id);
                if ($gallery) {
                    $filePath = $this->userThumbImagePath.$gallery->avtar;
                    if (Storage::disk($this->fileSystemCloud)->exists($filePath)) {
                        Storage::disk($this->fileSystemCloud)->delete($filePath);
                        Storage::disk($this->fileSystemCloud)->put($filePath, $file);
                    }
                }  
            }
            // Upload User Photo
            if (!empty($request->file('avtar')) && $request->file('avtar')->isValid()) {
                $params = [
                        'originalPath'  => $this->userOriginalImagePath,
                        'thumbPath'     => $this->userThumbImagePath,
                        'thumbHeight'   => $this->userThumbImageHeight,
                        'thumbWidth'    => $this->userThumbImageWidth,
                        'previousImage' => $request->old_avtar,
                    ];

                    $file = $request->file('avtar');
                    if (!empty($request->get('avtarImg'))) {
                        $gallery = $request->get('avtarImg');
                        $base64_str = substr($gallery, strpos($gallery, ",")+1);
                        $decodedFile = base64_decode($base64_str);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = Str::random(20) . '.' . $extension;
                        $params['fileName'] = $fileName;
                        $avtarFile = Storage::disk('public')->put($this->userThumbImagePath.$fileName, $decodedFile);
                        $userPhoto = ImageUpload::uploadMedia($file, $params);
                    } else {
                        $userPhoto = ImageUpload::uploadWithThumbImage($file, $params);
                    }
                if ($userPhoto === false) {
                    DB::rollback();
                    Alert::error('Error', trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE') , 'error');
                }

                $old_image = User::select('avtar')->where('id',$id)->first();
                if(!empty($old_image->avtar)){
                    $image_original_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$old_image->avtar);
                    $image_thumb_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH').$old_image->avtar);

                    if (file_exists($image_original_path)) {
                        @unlink($image_original_path);
                    }
                    if (file_exists($image_thumb_path)) {
                        @unlink($image_thumb_path);
                    }
                }
                $input['avtar'] = $userPhoto['imageName'];
            }

			$phone = "";
			if(!empty($request->phone)){
				$phoneNumber = trim($request->phone);
				$phone = convertPhoneToInt($phoneNumber);
			}

            $input['name']		= trim($request->name);
            $input['user_name'] = trim($request->user_name);
            $input['gender']	= trim($request->gender);
            $input['phone']		= $phone;
            $input['email']		= trim($request->email);
            $input['status']    = $request->status;
            User::where('id', $id)->update($input);

           ConsumerProfileDetail::updateOrCreate(
                ['user_id' => $id],
                [
					'address'	=> trim($request->get('address')),
					'unit_apt'	=> trim($request->get('unit_apt')),
					'city' 		=> trim($request->city),
					'state' 	=> trim($request->state),
					'country' 	=> trim($request->country),
					'zipcode' 	=> trim($request->zipcode),
					'update_data' => 1,
                ]
            );

            Alert::success('Success', 'Employee user details updated.', 'success');
		    return redirect('business/users/employee-users');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/users/employee-users');
        }
	}

    /*
        @Author : Spec Developer
        @Desc   : View employee user details.
        @Date   : 02/03/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;

		$user = User::where('id', $id)->with('consumer')->first();
        $results = new UserRegisterResource($user);

        $data['old_avtar'] = $results->avtar;
        $data['address'] = !empty($results->consumer->address) ? $results->consumer->address : '-';
        $data['unit_apt'] = !empty($results->consumer->unit_apt) ? $results->consumer->unit_apt : '-';
        $data['city'] = !empty($results->consumer->city) ? getCityName($results->consumer->city) : '-';
        $data['state'] = !empty($results->consumer->state) ? getStateName($results->consumer->state) : '-';
        $data['country'] = !empty($results->consumer->country) ? getCountryName($results->consumer->country) : '-';
        $data['zipcode'] =!empty($results->consumer->zipcode) ?  $results->consumer->zipcode : '-';
		$data['phone'] =!empty($results->phone) ?  convertPhoneToUsFormat($results->phone) : NULL;

        $results['avtar_url'] = !empty($results->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$results->avtar) : asset('backend/assets/images/no-avtar.png');
        $results['placeholder_url'] = asset('backend/assets/images/no-avtar.png');

        $data['data'] = $results;
        return view('business.users.view_employee_user',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete employee user record.
        @Output : \Illuminate\Http\Response
        @Date   : 22/02/2022
    */

    public function destroy($id){

        $objUser = ConsumerProfileDetail::where('user_id',$id)->first();
		$objUser->delete();

		$obj = User::find($id);

        $image_original_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$obj['icon_file']);
        $image_thumb_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH').$obj['icon_file']);

		if (file_exists($image_original_path)) {
			@unlink($image_original_path);
		}
        if (file_exists($image_thumb_path)) {
			@unlink($image_thumb_path);
		}
        $obj->delete();

        Alert::success('Success', 'Employee user has been deleted successfully!', 'success');
		return redirect('business/users/employee-users');
    }

	/*
        @Author : Spec Developer
        @Desc   : Change staus of employee user.
        @Output : \Illuminate\Http\Response
        @Date   : 21/03/2022
    */

    public function changeStatus(Request $request){

        $user_id = $request->input('user_id');
		$new_status = $request->input('new_status');
		$validator = $request->validate([
			'user_id'		=>	'required',
			'new_status'	=>	'required',
		]);
		$obj = User::find($user_id);
		$obj->status    = $new_status;
		$obj->save();
		Alert::success('Success', 'Status has been changed successfully!', 'success');
		return redirect('business/users/employee-users');
    }


}