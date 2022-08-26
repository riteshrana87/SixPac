<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class AdvertismentController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function index(Request $request){
    	$data['page_title'] = 'Advertisment';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/advertisment.js'
        );
        $data['extra_css'] = array(
            //'assets/css/scrollspyNav.css',
        );
		$data['cdnurl_css'] = array(
            'plugins/table/datatable/datatables.css'
        );
		$data['cdnurl_js'] = array(
            'plugins/table/datatable/datatables.js',
        );
		$data['extra_js'] = array(
            //'assets/js/scrollspyNav.js'
        );

		$data['init'] = array(
            'Advertisment.init();'
        );
        return view('superadmin.advertisment.advertisment_list',$data);
    }
}