<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use App\Models\DeviceToken;
use App\Models\ConsumerProfileDetail;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use DateTime;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use App\Http\Resources\UsersResource;
use App\Http\Resources\UserRegisterResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class SettingsController extends Controller
{
	use AuthenticatesUsers;

    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');

        $this->superAdminOriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->superAdminThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->superAdminThumbImageHeight = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->superAdminThumbImageWidth = Config::get('constant.USER_THUMB_PHOTO_WIDTH');
        $this->fileSystemCloud = Config::get('constant.FILESYSTEM_CLOUD');
    }

    /*
        @Author : Spec Developer
        @Desc   : Fetch business user details for edit profile.
        @Date   : 18/02/2022
    */

	public function editProfile(){
		$data['page_title'] = 'Edit Profile';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/settings.js'
        );
        $data['extra_css'] = array(
        );
		$data['cdnurl_css'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css'
        );
		$data['cdnurl_js'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js'
        );
		$data['extra_js'] = array(
			'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
        );

		$data['init'] = array(
            'Settings.init();'
        );
		$id = Auth::user()->id;

        $user = User::where('id', $id)->with('consumer')->with('business')->first();
        $results = new UserRegisterResource($user);


        $data['old_avtar'] = $results->avtar;

        $data['address'] = !empty($results->consumer->address) ? $results->consumer->address : NULL;
        $data['unit_apt'] = !empty($results->consumer->unit_apt) ? $results->consumer->unit_apt : NULL;
        $data['city'] = !empty($results->consumer->city) ? $results->consumer->city : NULL;
        $data['state'] = !empty($results->consumer->state) ? $results->consumer->state : NULL;
        $data['country'] = !empty($results->consumer->country) ? $results->consumer->country : NULL;
        $data['zipcode'] = !empty($results->consumer->zipcode) ?  $results->consumer->zipcode : NULL;
        $data['company_name'] = !empty($results->business->company_name) ?  $results->business->company_name : NULL;
        $data['company_url'] = !empty($results->business->company_url) ?  $results->business->company_url : NULL;
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

        return view('superadmin.settings.edit_profile',$data);
	}

    /*
        @Author : Spec Developer
        @Desc   : Update business user details for edit profile.
        @Output : \Illuminate\Http\Response
        @Date   : 18/02/2022
    */

	public function updateProfile(Request $request){

        try {
            $id = Auth::user()->id;

            $imgValidationArr = array();

            if(empty($request->input('pf_img'))){
                $imgValidationArr['profile_photo'] = 'sometimes|mimes:jpeg,png|max:1024|dimensions:min_width=100,max_width=800';
            }

            $textValidationArr = array(
                'name'	=>	'required|max:40',
                'email'	=>	'required',
                'phone'	=>	'required',
                'date_of_birth'	=>	'required',
                'user_name' => 'required|min:4|max:50|unique:users,user_name,'.$id
            );
            $validationArr = array_merge($textValidationArr,$imgValidationArr);


            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Edit super admin profile :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }
            if (empty($request->file('avtar')) && !empty($request->get('avtarImg'))) {           
               $gallery = $request->get('avtarImg');
               $base64_str = substr($gallery, strpos($gallery, ",")+1);
               $file = base64_decode($base64_str);                
                $gallery = User::find($id);
                if ($gallery) {
                    $filePath = $this->superAdminThumbImagePath.$gallery->avtar;
                    if (Storage::disk($this->fileSystemCloud)->exists($filePath)) {
                        Storage::disk($this->fileSystemCloud)->delete($filePath);
                        Storage::disk($this->fileSystemCloud)->put($filePath, $file);
                    }
                }  
            }
            // Upload User Photo
            if (!empty($request->file('profile_photo')) && $request->file('profile_photo')->isValid()) {
                $params = [
                        'originalPath' => $this->superAdminOriginalImagePath,
                        'thumbPath' => $this->superAdminThumbImagePath,
                        'thumbHeight' => $this->superAdminThumbImageHeight,
                        'thumbWidth' => $this->superAdminThumbImageWidth,
                        'previousImage' => $request->pf_img,
                    ];
                    $file = $request->file('profile_photo');
                    if (!empty($request->get('avtarImg'))) {
                        $gallery = $request->get('avtarImg');
                        $base64_str = substr($gallery, strpos($gallery, ",")+1);
                        $decodedFile = base64_decode($base64_str);
                        $extension = $file->getClientOriginalExtension();
                        $fileName = Str::random(20) . '.' . $extension;
                        $params['fileName'] = $fileName;
                        $avtarFile = Storage::disk('public')->put($this->superAdminThumbImagePath.$fileName, $decodedFile);
                        $userPhoto = ImageUpload::uploadMedia($file, $params);
                    } else {
                        $userPhoto = ImageUpload::uploadWithThumbImage($file, $params);
                    }
                if ($userPhoto === false) {
                    DB::rollback();
                    Alert::error('Error', trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE') , 'error');
                    //return redirect()->back()->withErrors(trans('log-message.IMAGE_UPLOAD_ERROR_MESSAGE'))->withInput();
                }
                $input['avtar'] = $userPhoto['imageName'];
            }

            if(!empty(Auth::user()->avtar) && empty($request->input('pf_img'))){
                $image_original_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').Auth::user()->avtar);
                $image_thumb_path = Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->path(Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH').Auth::user()->avtar);

                if (file_exists($image_original_path)) {
                    @unlink($image_original_path);
                }
                if (file_exists($image_thumb_path)) {
                    @unlink($image_thumb_path);
                }
            }

            $dateTimeStamp = DateTime::createFromFormat('!d/m/Y', $request->input('date_of_birth'))->getTimestamp();

			$phone = "";
			if(!empty($request->phone)){
				$phoneNumber = trim($request->phone);
				$phone = convertPhoneToInt($phoneNumber);
			}

            $input['date_of_birth'] = date('Y-m-d',$dateTimeStamp);
            $input['gender'] 		= $request->gender;
            $input['name'] 			= trim($request->name);
            $input['user_name']           = trim($request->user_name);
            $input['phone'] 		= $phone;
            $input['email'] 		= trim($request->email);
            //dd($input);
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

            Alert::success('Success', 'Profile details updated.', 'success');
		    return redirect('superadmin/settings/edit-profile');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/settings/edit-profile');
        }


	}

    /*
        @Author : Spec Developer
        @Desc   : Check email address is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 18/02/2022
    */

    public function validateUserEmail(Request $request){

        $userId = $request->id;
        if(!empty($userId)){
            $result = User::where('email', $request->email)->whereNotIn('id', [$userId])->count();
        }
        else
        {
            $result = User::where('email', $request->email)->count();
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
        @Desc   : Check phone number is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 18/02/2022
    */

    public function validateUserPhone(Request $request){
        $userId = $request->id;
		$phone	= convertPhoneToInt($request->phone);

        if(!empty($userId)){
            $result = User::where('phone', $phone)->whereNotIn('id', [$userId])->count();
        }
        else
        {
            $result = User::where('phone', $phone)->count();
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
        @Desc   : Get change password form.
        @Date   : 18/02/2022
    */

    public function changePassword(){
		$data['page_title'] = 'Change Password';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/settings.js'
        );
        $data['extra_css'] = array(
        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(

        );
		$data['extra_js'] = array(
			'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
        );

		$data['init'] = array(
            'Settings.changePassword();'
        );

		$data['user_id'] = auth()->user()->id;

        return view('superadmin.settings.change_password',$data);
	}

    /*
        @Author : Spec Developer
        @Desc   : Change password for business user.
        @Output : \Illuminate\Http\Response
        @Date   : 18/02/2022
    */

	public function updatePassword(Request $request){
		try {
            $id = Auth::user()->id;

            $validator = Validator::make($request->all(), [
                'current_password'	=>	'required',
			    'new_password'		=>	'required',
                'confirm_password'  =>	'required',
            ]);

            if ($validator->fails()) {
                Log::info('Super admin change password details :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $data = $request->all();
            $user = auth()->user();

            if (!Hash::check($data['current_password'], $user->password)) {
                //return back()->with('error', 'The current password does not match!');
                return redirect('superadmin/settings/change-password')->with('error', 'The current password does not match!');
            }
            else
            {
                if ($request['current_password'] == $request['new_password']) {
                    //return back()->with('error', 'Your new password should be different from current password!');
                    return redirect('superadmin/settings/change-password')->with('error', 'Your new password should be different from current password!');
                }
                else
                {
                    $obj = User::find($id);
                    $obj->password 	= Hash::make($request['new_password']);
                    $obj->save();

                    Auth::attempt([
                        'email' => $obj->email,
                        'password' => $request['new_password']
                    ], 1);

                    Auth::logoutOtherDevices($request['new_password']);

                    DeviceToken::where(array('user_id' => $id))->delete();

                    DB::table('oauth_access_tokens')->where('user_id', $id)->delete();

                    //Alert::toast('Password has been updated! Please login with your new password.', 'success')->autoclose(3500);
                    //return redirect('login');
                    Alert::toast('Password has been updated!', 'success')->autoclose(3500);
                    return redirect('superadmin/settings/change-password');
                }
            }
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/settings/change-password');
        }
	}

	/*
        @Author : Spec Developer
        @Desc   : Get state from country id.
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */

    public function getState(Request $request){
        $countryId = $request->country_id;
        //$data = State::select('id','name')->where('country_id',$countryId)->orderBy('name','asc')->get();
         $data = Country::select('states.id as id','states.name as name')
			->join('states','states.country_id','=','countries.id')
			->join('cities','cities.state_id','=','states.id')
			->where('states.country_id',$countryId)
			->orderby('states.name','ASC')
			->groupby('states.id')
			->get();
        $response = '<option value="">Select State</option>';
        if(count($data) > 0){
            foreach($data as $state){
                $response .= '<option value="'.$state->id.'">'.$state->name.'</option>';
            }
        }
        return $response;
    }

    /*
        @Author : Spec Developer
        @Desc   : Get city from country id.
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */

    public function getCity(Request $request){
        $cId = $request->country_id;
        $sId = $request->state_id;
        $data = City::select('id','name')->where(['country_id' => $cId, 'state_id' => $sId])->orderBy('name','asc')->get();
        $response = '<option value="">Select City</option>';
        if(count($data) > 0){
            foreach($data as $city){
                $response .= '<option value="'.$city->id.'">'.$city->name.'</option>';
            }
        }
        return $response;
    }

    public function getCityData(Request $request) {
        try {
            $cityId = $request->get('id');
            $result = [];
            if (!empty($cityId)) {
                $cityData = City::where('id', $cityId)
                ->with(['getCityState','getCityState.getStateCountry'])
                ->first();
                $result['state_name'] = !empty($cityData->getCityState) ? $cityData->getCityState['name'] : null;
                $result['state_id'] = !empty($cityData->getCityState) ? $cityData->getCityState['id'] : null;
                $result['country_name'] = !empty($cityData->getCityState->getStateCountry) ? $cityData->getCityState->getStateCountry['name'] : null;
                $result['country_id'] = !empty($cityData->getCityState->getStateCountry) ? $cityData->getCityState->getStateCountry['id'] : null;
                $result['status'] = true;
                $result['message'] = 'Success';
            }
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['message'] = $e->getMessage();
        }
        echo json_encode($result);die;
    }

    /*
        @Author : Spec Developer
        @Desc   : Get US Country state and city from country id.
        @Output : \Illuminate\Http\Response
        @Date   : 29/03/2022
    */

    // public function getUsStateAndCity(Request $request){
    //     $countryId = $request->country_id;
    //     $response['state'] = State::select('id','name')->where('country_id',$countryId)->orderby('name','asc')->get();
    //     $response['city'] = City::select('id','name')->where('country_id',$countryId)->orderby('name','asc')->get();
    //     return json_encode($response);
    // }
}