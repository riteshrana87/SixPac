<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
class PasswordResetSuccess extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
	protected $redirectTo = '/login';
	
    public function __construct()
    {
        /* $this->middleware('auth'); */
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		Auth::logout();
		return view('password-message');
    }
}
