<?php

namespace App\Http\Controllers\Web\Business;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function index(){
		return view('business.dashboard');
	}
}