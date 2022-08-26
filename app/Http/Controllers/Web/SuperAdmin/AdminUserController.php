<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ConsumerProfileDetail;
use App\Http\Resources\UserRegisterResource;
use Hash;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\DataTableService;

class AdminUserController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->userOriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->userThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->userThumbImageHeight = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->userThumbImageWidth = Config::get('constant.USER_THUMB_PHOTO_WIDTH');
        $this->userImportPath = Config::get('constant.IMPORT_ADMIN_USER_CSV_UPLOAD_PATH');
        $this->userExportPath = Config::get('constant.EXPORT_ADMIN_USER_CSV_UPLOAD_PATH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
        $this->dataTable = new DataTableService();
    }
	/*
		@Author : Spec Developer
		@Desc   : Fetch admin user listing
		@Output : \Illuminate\Http\Response
		@Date   : 02/03/2022
	*/
    public function index(Request $request){

    	$data['page_title'] = 'Admin Users';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/admin_users.js'
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
            //'assets/js/scrollspyNav.js'
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
            'AdminUsers.init();'
        );
        if ($request->ajax()) {
            $select = ['id','name','email','phone','created_at','role','status'];
            $query = User::whereNotIn('id', [Auth::user()->id])
            ->where(['role' => 2]);
            $search = ['name', 'email', 'phone'];
            $actions = [
                'view' => url('superadmin/users/admin-users/view'),
                'edit' => url('superadmin/users/admin-users/edit'),
                'delete' => url('superadmin/users/admin-users/destroy/')
            ];
           $this->dataTable->showTable($request,$query,$select, $search, $actions, 1);
        }
        $filename = 'admin_users.csv';
        $data['csvFile'] = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.EXPORT_ADMIN_USER_CSV_UPLOAD_PATH').$filename);

        return view('superadmin.users.admin_user_list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add admin user.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Admin User';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/admin_users.js'
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
            'AdminUsers.add();'
        );
		$data['placeholder_url'] = asset('backend/assets/images/no-avtar.png');

        return view('superadmin.users.add_admin_user',$data);
	}

	/*
		@Author : Spec Developer
		@Desc   : Store new admin user data.
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
                Log::info('Add admin user by super admin :: message :' . $validator->errors());
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
				'role' 		=> 2,
				'created_by' => Auth::user()->id,
                'avtar'     => $avtar,
            ]);
            $objUser->save();
            $userId = $objUser->id;

            $obj = new ConsumerProfileDetail([
                'user_id'   	=> $userId,
                'address' 		=> trim($request->get('address')),
                'unit_apt' 		=> trim($request->get('unit_apt')),
                'city' 			=> trim($request->get('city')),
                'state' 		=> trim($request->get('state')),
                'country' 		=> trim($request->get('country')),
                'zipcode' 		=> trim($request->get('zipcode')),
            ]);
            $obj->save();

            Alert::success('Success', 'Admin user has been added!.', 'success');
		    return redirect('superadmin/users/admin-users');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/users/admin-users/add');
        }
    }

    /*
		@Author : Spec Developer
		@Desc   : Edit admin user.
		@Output : \Illuminate\Http\Response
		@Date   : 02/03/2022
	*/

    public function editAdminUser($id){

        $data['page_title'] = 'Edit Admin User';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/admin_users.js'
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
            'AdminUsers.edit();'
        );

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
        return view('superadmin.users.edit_admin_user',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Update admin user details.
        @Output : \Illuminate\Http\Response
        @Date   : 02/03/2022
    */

	public function updateAdminUser(Request $request){

        try {
            // dd($request->all());
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
                Log::info('Edit admin user by super admin :: message :' . $validator->errors());
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
            $input['user_name']     = trim($request->user_name);
            $input['gender']	= trim($request->gender);
            $input['phone']		= $phone;
            $input['email']		= trim($request->email);
            $input['status']    = $request->status;

            User::where('id', $id)->update($input);

            ConsumerProfileDetail::updateOrCreate(
                ['user_id' => $id],
                [
					'address'	=> trim($request->address),
					'unit_apt'	=> trim($request->unit_apt),
					'city' 		=> trim($request->city),
					'state' 	=> trim($request->state),
					'country' 	=> trim($request->country),
					'zipcode' 	=> trim($request->zipcode),
					'update_data' => 1,
                ]
            );

            Alert::success('Success', 'Admin user details updated.', 'success');
		    return redirect('superadmin/users/admin-users');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/users/admin-users');
        }
	}

    /*
        @Author : Spec Developer
        @Desc   : View admin user details.
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
		$data['phone'] = !empty($results->phone) ?  convertPhoneToUsFormat($results->phone) : NULL;


        $results['avtar_url'] = !empty($results->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$results->avtar) : asset('backend/assets/images/no-avtar.png');
        $results['placeholder_url'] = asset('backend/assets/images/no-avtar.png');

        $data['data'] = $results;
        return view('superadmin.users.view_admin_user',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete admin user record.
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

        Alert::success('Success', 'Admin user has been deleted successfully!', 'success');
		return redirect('superadmin/users/admin-users');
    }

    /*
        @Author : Spec Developer
        @Desc   : Change staus of admin user.
        @Output : \Illuminate\Http\Response
        @Date   : 09/03/2022
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
		return redirect('superadmin/users/admin-users');
    }

    /*
        @Author : Spec Developer
        @Desc   : Import csv file users.
        @Output : \Illuminate\Http\Response
        @Date   : 09/03/2022
    */

    public function importAdminUsers(Request $request){
        $file = $request->file('csv_file');
        if ($file) {
            // File Details
            $filename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $tempPath = $file->getRealPath();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Valid File Extensions
            $valid_extension = array("csv");

            // 2MB in Bytes
            $maxFileSize = 2097152;

            // Check file extension
            if(in_array(strtolower($extension),$valid_extension)){
                    // Check file size
                if($fileSize <= $maxFileSize){

                    // File upload location
                    // $location = $this->userImportPath;

                    $filePath = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.IMPORT_ADMIN_USER_CSV_UPLOAD_PATH'));
                    $file->move($filePath,$filename);

                    // Reading file
                    $file = fopen($filePath.'/'.$filename,"r");

                    $importData_arr = array();
                    $i = 0;

                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );

                        // Skip first row (Remove below comment if you want to skip the first row)
                        if($i == 0){
                            $i++;
                            continue;
                        }
                        for ($c=0; $c < $num; $c++) {
                            $importData_arr[$i][] = $filedata [$c];
                        }
                        $i++;
                    }
                    fclose($file);

                    // Insert to MySQL database
                    foreach($importData_arr as $importData){
                        $role = 2;
                        $gender = 2;
                        if(strtolower(trim($importData[5])) == 'male'){
                            $gender = 1;
                        }
                        //$dobExp = explode('-',$importData[3]);
                        //$dob = $dobExp[2].'-'.$dobExp[1].'-'.$dobExp[0];
                        $userId = '';
                        $phoneNumber = convertPhoneToInt(trim($importData[3]));
                        // Check email or phone number exists?
                        $countUser = User::where('role', $role)
                        ->where(function ($query) use ($importData, $phoneNumber) {
                            return $query->where('email', trim($importData[2]))
                            ->orWhere('phone', $phoneNumber)
                            ->orWhere('user_name', trim($importData[1]));
                        })->first();
                        if ($countUser) {
                             // Update existing record
                            $input['name']      =   trim($importData[0]);
                            $input['user_name']     =   trim($importData[1]);
                            $input['email']     =   trim($importData[2]);
                            $input['phone']     =   $phoneNumber;
                            $input['date_of_birth']    = trim($importData[4]);
                            $input['gender']    = $gender;
                            $input['updated_by']    =   1;
                            $input['updated_at']    =  Carbon::now();
                            User::where('id',$countUser->id)->update($input);
                            $userId = $countUser->id;
                        }
                        else
                        {
                            // $ifExistingUser = User::where('email', trim($importData[2]))->orWhere('phone', $phoneNumber)->orWhere('name', trim($importData[0]))->orWhere('user_name', trim($importData[1]))->exists();
                            // if ($ifExistingUser == 0) {
                                // Add new record
                                $obj = new User([
                                    "name"  =>  trim($importData[0]),
                                    "user_name" => trim($importData[1]),
                                    "email" =>  trim($importData[2]),
                                    "phone" =>  $phoneNumber,
                                    "date_of_birth" =>  trim($importData[4]), //$dob,
                                    "gender"        =>  $gender,
                                    "created_by"    =>  1,
                                    "role"          =>  $role,
                                    "status"        =>  1,
                                ]);
                                $obj->save();
                                $userId = $obj->id;
                            // }
                        }

                        if(!empty($userId)){

                            $address    = (!empty(trim($importData[6])) || trim($importData[6]) != '-') ? trim($importData[6]) : NULL;
                            $unitApt    = (!empty(trim($importData[7])) || trim($importData[7]) != '-') ? trim($importData[7]) : NULL;

							$city    = (!empty(strtolower(trim($importData[8]))) || trim(strtolower(trim($importData[8]))) != '-') ? trim($importData[8]) : NULL;
							$state    = (!empty(strtolower(trim($importData[9]))) || trim(strtolower(trim($importData[9]))) != '-') ? trim($importData[9]) : NULL;
                            $country    = (!empty(strtolower(trim($importData[10]))) || trim(strtolower(trim($importData[10]))) != '-') ? trim($importData[10]) : NULL;
                            $zipCode    = (!empty(trim($importData[11])) || trim($importData[11]) != '-') ? trim($importData[11]) : NULL;
							$locationRecord = Country::select('countries.id as country_id','countries.name as country','states.id as state_id','states.name as state','cities.id as city_id','cities.name as city')
								->leftjoin('states','states.country_id','=','countries.id')
								->leftjoin('cities','cities.state_id','=','states.id')
								->whereRaw("LOWER(countries.name) = ?", [$country])
								->whereRaw("LOWER(states.name) = ?", [$state])
								->whereRaw("LOWER(cities.name) = ?", [$city])
								->first();

							$countryId = NULL;
							$stateId = NULL;
							$cityId = NULL;

							if(!empty($locationRecord)){
								$countryId =  !empty($country) ?  getCountryId($country) : NULL;
								$stateId =  !empty($state) ?  getStateId($state) : NULL;
								$cityId =  !empty($city) ?  getCityId($city) : NULL;
							}

                            ConsumerProfileDetail::updateOrCreate(
                                ['user_id' => $userId],
                                [
                                    'address'   => $address,
                                    'unit_apt'  => $unitApt,
                                    'city'      => $cityId,
                                    'state'     => $stateId,
                                    'country'   => $countryId,
                                    'zipcode'   => $zipCode,
                                    'update_data' => 1,
                                ]
                            );
                        }
                    }

                    $csv_file_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.IMPORT_ADMIN_USER_CSV_UPLOAD_PATH').$filename);


                    if (file_exists($csv_file_path)) {
                        @unlink($csv_file_path);
                    }

                    $errorCode = 1;
                }
                else
                {
                    $errorCode = 0;
                }
            }
            else
            {
                $errorCode = 3;
            }
                return $errorCode;
            }
        }

    /*
        @Author : Spec Developer
        @Desc   : Export admin user into csv.
        @Output : \Illuminate\Http\Response
        @Date   : 10/03/2022
    */

    public function exportAdminUsers(Request $request){

        if($request->action == "exportAdminUser"){

            //$users = User::select('name','email','phone','date_of_birth','gender')->where('role',2)->orderby('id','desc')->get();

            $delimiter = ",";
            $fileName = 'admin_users.csv';
            $filePath = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.EXPORT_ADMIN_USER_CSV_UPLOAD_PATH').$fileName);

            $f = fopen($filePath, 'w');
            $fields = array('Name', 'Username', 'Email', 'Phone', 'Date of Birth', 'Gender','Address','Unit/Apt #','City','State','Country','ZIP or Postal Code');
            fputcsv($f, $fields, $delimiter);

            $results = User::with(array('consumer'=> function($query) {
                //$query->select('city','state','country','zipcode');
            }))->where('role',2)
            ->orderby('id','DESC')
            ->get();


            foreach ($results as $users) {

                $name =!empty($users->name) ?  $users->name : '-';
                $username = !empty($users->user_name) ?  $users->user_name : '-';
                $email =!empty($users->email) ?  $users->email : '-';
                $phone =!empty($users->phone) ?  convertPhoneToUsFormat($users->phone) : '-';
                $date_of_birth = (!empty($users->date_of_birth) || $users->date_of_birth == "0000-00-00") ?  $users->date_of_birth : '-';
                $gender = $users->gender;

                $address = !empty($users->consumer->address) ? $users->consumer->address : '-';
                $unit_apt = !empty($users->consumer->unit_apt) ? $users->consumer->unit_apt : '-';
                $city = !empty($users->consumer->city) ? getCityName($users->consumer->city) : '-';
                $state = !empty($users->consumer->state) ? getStateName($users->consumer->state) : '-';
                $country = !empty($users->consumer->country) ? getCountryName($users->consumer->country) : '-';
                $zipcode =!empty($users->consumer->zipcode) ?  $users->consumer->zipcode : '-';


                if($gender == 1){$gender = 'Male';}
                if($gender == 2){$gender = 'Female';}
                if($gender == 3){$gender = 'Other';}

                $lineData = array($name, $username, $email, $phone, $date_of_birth, $gender, $address, $unit_apt, $city, $state, $country,$zipcode);
                fputcsv($f, $lineData, $delimiter);
            }
            fclose($f);
            exit;
        }
        else
        {
            // error
        }
    }
}