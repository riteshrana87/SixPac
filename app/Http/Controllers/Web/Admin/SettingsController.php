<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use App\Models\DeviceToken;
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
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
	use AuthenticatesUsers;

    public function __construct()
    {
		//$this->middleware('preventBackHistory');
        $this->middleware('auth');

        $this->adminOriginalImagePath = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->adminThumbImagePath = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->adminThumbImageHeight = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->adminThumbImageWidth = Config::get('constant.USER_THUMB_PHOTO_WIDTH');

    }

    /*
        @Author : Spec Developer
        @Desc   : Fetch admin details for edit profile.
        @Date   : 18/02/2022
    */
	public function editProfile(){
		$data['page_title'] = 'Edit Profile';
		$data['page_js'] = array(
            'backend/assets/admin/js/settings.js'
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
		$users = auth()->user();
        $data['old_avtar'] = $users->avtar;

        $users->avtar_url = !empty($users->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$users->avtar) : asset('backend/assets/images/no-avtar.png');
        $users->placeholder_url = asset('backend/assets/images/no-avtar.png');

        $data['profile'] = $users;
        return view('admin.settings.edit_profile',$data);
	}

    /*
        @Author : Spec Developer
        @Desc   : Update admin details for edit profile.
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
                'name'	=>	'required',
                'email'	=>	'required',
                'phone'	=>	'required',
                'date_of_birth'	=>	'required',
            );
            $validationArr = array_merge($textValidationArr,$imgValidationArr);


            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Edit admin profile :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Upload User Photo
            if (!empty($request->file('profile_photo')) && $request->file('profile_photo')->isValid()) {
                $params = [
                        'originalPath' => $this->adminOriginalImagePath,
                        'thumbPath' => $this->adminThumbImagePath,
                        'thumbHeight' => $this->adminThumbImageHeight,
                        'thumbWidth' => $this->adminThumbImageWidth,
                        'previousImage' => $request->pf_img,
                    ];

                $userPhoto = ImageUpload::uploadWithThumbImage($request->file('profile_photo'), $params);
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

            $input['date_of_birth'] = date('Y-m-d',$dateTimeStamp);
            $input['gender'] = $request->gender;
            $input['name'] = $request->name;
            $input['phone'] = $request->phone;
            $input['email'] = $request->email;
            //dd($input);
            User::where('id', $id)->update($input);

            Alert::success('Success', 'Profile details updated.', 'success');
		    return redirect('admin/settings/edit-profile');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('admin/settings/edit-profile');
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
        if(!empty($userId)){
            $result = User::where('phone', $request->phone)->whereNotIn('id', [$userId])->count();
        }
        else
        {
            $result = User::where('phone', $request->phone)->count();
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
            'backend/assets/admin/js/settings.js'
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

        return view('admin.settings.change_password',$data);
	}

    /*
        @Author : Spec Developer
        @Desc   : Change password for admin user.
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
                return redirect('admin/settings/change-password')->with('error', 'The current password does not match!');
            }
            else
            {
                if ($request['current_password'] == $request['new_password']) {
                    //return back()->with('error', 'Your new password should be different from current password!');
                    return redirect('admin/settings/change-password')->with('error', 'Your new password should be different from current password!');
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
                    return redirect('admin/settings/change-password');
                }
            }
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('admin/settings/change-password');
        }
	}
}
