<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function index(){
        $data['page_title'] = 'Dashboard';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/dashboard.js'
        );
        $data['extra_css'] = array(

        );
		$data['extra_js'] = array(

        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array(
            '//cdn.amcharts.com/lib/4/core.js',
            '//cdn.amcharts.com/lib/4/charts.js',
            '//cdn.amcharts.com/lib/4/themes/animated.js',
        );

		$data['init'] = array(
            'Dashboard.init();'
        );
        //cdn.amcharts.com/lib/5/index.js
        return view('superadmin.dashboard', $data);
	}

    public function getYearWiseUser(Request $request){
        $year = $request->year;

        $data = User::select('id','created_at')->withCount('adminData')->withCount('consumerData')->withCount('businessData')
        ->whereYear('created_at',$year)->get();

        $arr = $months = [];

        $months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
        foreach($months as $month) {
            $arr[$month]['month'] = $month;
            $arr[$month]['admin'] = 0;
            $arr[$month]['business'] = 0;
            $arr[$month]['consumer'] = 0;
        }

        foreach($data as $value) {
            $createdDate = strtoupper(Carbon::parse($value->created_at)->format('M'));
            if (array_key_exists($createdDate,$arr))
            {
                if (!empty($arr[$createdDate]['admin'])) {
                    $arr[$createdDate]['admin'] += !empty($value->adminData) ? 1 : 0;
                } else {
                    $arr[$createdDate]['admin'] = !empty($value->adminData) ? 1 : 0;
                }

                if (!empty($arr[$createdDate]['consumer'])) {
                    $arr[$createdDate]['consumer'] += !empty($value->consumer_data_count) ? 1 : 0;
                } else {
                    $arr[$createdDate]['consumer'] = !empty($value->consumer_data_count) ? 1 : 0;
                }

                if (!empty($arr[$createdDate]['business'])) {
                    $arr[$createdDate]['business'] += !empty($value->business_data_count) ? 1 : 0;
                } else {
                    $arr[$createdDate]['business'] = !empty($value->business_data_count) ? 1 : 0;
                }
            }
        }
        return array_values($arr);
    }
}