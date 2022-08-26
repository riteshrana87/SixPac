<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function index(Request $request){
    	$data['page_title'] = 'Users';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/users.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css'
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'Users.init();'
        );
        if ($request->ajax()) {
            $userdata = User::select('id','name','email','phone','role','created_by','status')->whereNotIn('id', [Auth::user()->id])->orderBy('created_at','DESC')->get();

            return Datatables::of($userdata)
                ->addColumn('name', function($row){
                    $name = 'No name';
                    if(!empty($row->name)){
                        $name = $row->name;
                    }
                    return '<p class="align-self-center mb-0 avtext">'.$name.'</p></div>';
                })
                ->addColumn('email', function($row){
                    if($row->email == ""){
                        $row->email = '-';
                    }
                    return $row->email;
                })
                ->addColumn('phone', function($row){
                    $phone = '-';
                    if(!empty($row->phone)){
                        $phone = $row->phone;
                    }
                    return $phone;
                })

                ->addColumn('role', function($row){
                    $role = '-';
                    if($row->role == 1){
                        $role = 'Super Admin';
                    }
                    if($row->role == 2){
                        $role = 'Admin';
                    }
					if($row->role == 3){
                        $role = 'Business';
                    }
                    if($row->role == 4){
                        $role = 'Employee';
                    }
                    if($row->role == 5){
                        $role = 'Consumer';
                    }
                    return $role;
                })
                ->addColumn('created_by', function($row){
                    if($row->created_by == 0 || $row->created_by == NULL){
                        $created_by = "Self";
                    }
					else{
						$created_by = $row->created_by;
					}
                    return $created_by;
                })
                ->addColumn('status', function($row){
                    $status = '<label class="label label-success">Active</label>';
                    if($row->status == 0){
                        $status = '<label class="label label-danger">Deactive</label>';
                    }
                    return $status;
                })
                ->addColumn('action', function($row){
                    if($row->user_type == 1){
                        $btn = '';
                    }
                    else
                    {
                        $btn = '';
						$btn .= '<a class="viewRec ml-2 mr-2" href="'.url('myadmin/users/details/').'/'.$row->id.'" title="Details"><i class="fa fa-search-plus fa-action-view"></i></a>';

                        $btn .= '<a class="viewRec ml-2 mr-2" href="'.url('myadmin/users/details/').'/'.$row->id.'" title="Details"><i class="fa fa-pencil fa-action-edit"></i></a>';

                        $btn .= '<a class="viewRec ml-2 mr-2" href="'.url('myadmin/users/details/').'/'.$row->id.'" title="Details"><i class="fa fa-trash fa-action-delete"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
        }
        return view('superadmin.users.user_list',$data);
    }
}