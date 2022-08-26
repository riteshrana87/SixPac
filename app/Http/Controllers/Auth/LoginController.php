<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

	public function redirectTo()
    {
        getUserTimeZone();
        switch(Auth::user()->role){
            case 1:
                $this->redirectTo = '/superadmin/dashboard';
                return $this->redirectTo;
                break;
			case 2:
				$this->redirectTo = '/admin/dashboard';
				return $this->redirectTo;
                break;
            case 3:
                $this->redirectTo = '/business/dashboard';
                return $this->redirectTo;
                break;
            case 4:
                $this->redirectTo = '/employee/dashboard';
                return $this->redirectTo;
                break;
            case 5:
                $this->redirectTo = '/consumer/dashboard';
                return $this->redirectTo;
                break;
            default:
                $this->redirectTo = '/login';
                return $this->redirectTo;
        }
        // return $next($request);
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }



    // protected function credentials(Request $request)
    // {
    //     if(is_numeric($request->get('email'))){
    //         return ['phone'=>$request->get('email'),'password'=>$request->get('password')];
    //     }
    //    return $request->only($this->username(), 'password');
    // }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */

    protected function credentials(Request $request)
    {

        /* $phoneNumber = str_replace('-', '', $request->get('email'));
        $phone = preg_replace('/[^A-Za-z0-9\-]/', '', $phoneNumber);

        if(is_numeric($phone) && $request->get('loginWith') == 2){
			return ['phone'=>$phone,'password'=>$request->get('password')];
        }
        elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->get('email'), 'password'=>$request->get('password')];
        }
        return ['email' => $request->get('email'), 'password'=>$request->get('password')]; */

		/* $phoneNumber = str_replace('-', '', $request->get('email'));
        $phone = preg_replace('/[^A-Za-z0-9\-]/', '', $phoneNumber); */

        if(is_numeric($request->get('email'))){
			return ['phone'=>$request->get('email'),'password'=>$request->get('password')];
        }
        elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->get('email'), 'password'=>$request->get('password')];
        }
        return ['email' => $request->get('email'), 'password'=>$request->get('password')];
    }

    protected function authenticated(Request $request, $user)
    {
        //alert()->success('Your login successfull.', 'Welcome to SixPac portal.');
        //Alert::success('Congrats', 'Your login successfull.', 'success')->autoclose(3500);
        Alert::toast('You are successfully logged in.', 'success')->autoclose(3500);
    }

    public function logout(Request $request) {
        Auth::logout();
        Session::flush();
        Alert::toast('You are successfully logout.', 'success')->autoclose(3500);
        return redirect('/login');
    }

}