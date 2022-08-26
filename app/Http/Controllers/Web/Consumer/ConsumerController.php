<?php

namespace App\Http\Controllers\Web\Consumer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ConsumerController extends Controller
{
    public function index(){
		return view('consumer.dashboard');
	}
}
