<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestForm extends Controller
{
    public function formExample(Request $request){
        $data['page_title'] = 'Test Form';
		$data['page_js'] = array(

        );
        $data['extra_css'] = array(

        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(
        );
		$data['extra_js'] = array(

        );

		$data['init'] = array(

        );

        return view('business.test_form');
    }
}