<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;

use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Session;
use Hash;
use Illuminate\Support\Facades\Auth;
use Redirect;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */

    // protected $redirectTo = RouteServiceProvider::HOME;

    protected $redirectTo = '/password-reset-done';

	protected function rules()
	{
		return [
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|min:6|confirmed',
		];
	}

	public function reset(Request $request)
    {
		$userRs = User::where(array('email'=>$request->email))->first();

		#Hash::make($request->password);
		if(empty($userRs)){
			return Redirect::back()->withErrors(['We can not find any user related this email address!']);
		}
		else
		{
			$userRw = $userRs;
			$userType = 'users';


			if (!Hash::check($request->password, $userRw->password, [])) {

				$this->validate($request, $this->rules(), $this->validationErrorMessages());


				$response = $this->broker($userType)->reset(
					$this->credentials($request), function ($user, $password) {
						$this->resetPassword($user, $password);
					}
				);

				return $response == Password::PASSWORD_RESET
					? $this->sendResetResponse($request, $response,$userRw->id, $userType)
					: $this->sendResetFailedResponse($request, $response);
			}
			else
			{
				return Redirect::back()->withErrors(['Your new password should be different from current password!']);
			}
		}
    }

	protected function sendResetResponse(Request $request,$response,$userId, $userType)
    {
		\Laravel\Passport\Token::where('user_id', $userId)->delete();
		if ($request->expectsJson()) {
            return response()->json([
                'status' => trans($response)
            ]);
        }

		if($userType == "users"){
			//return redirect('/login')->with('status', trans($response));
            Auth::logout();
            Session::flush();
            Alert::toast(trans($response), 'success')->autoclose(3500);
            return redirect('/login');
		}
		else
		{
            return redirect($this->redirectPath())->with('status', trans($response));
		}
    }


}