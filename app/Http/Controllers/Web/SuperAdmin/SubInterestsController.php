<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Interests;
use App\Models\SubInterests;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SubInterestsResource;
use Illuminate\Auth\Access\AuthorizationException;

class SubInterestsController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /*
		@Author : Spec Developer
		@Desc   : Fetch sub interest listing.
		@Output : \Illuminate\Http\Response
		@Date   : 23/02/2022
	*/

    public function index(Request $request){

        $data['page_title'] = 'Sub Interests';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/sub_interests.js'
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
           'SubInterests.init();'
        );

		if ($request->ajax()) {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowPerPage = $request->get("length"); 
            $searchValue = $request->get('search')['value'] ?? '';
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $columnIndex = $columnIndex_arr[0]['column'];
            $columnName = $columnName_arr[$columnIndex]['data'];
            $sortOrder = $order_arr[0]['dir'];

            $totalRecords = SubInterests::with(array('interests'=> function($query) {
                $query->select('id','interest_name');
            }, 'usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))
            ->where(array('status' => 1))
            ->whereNotIn('sub_interest_name',['Other'])
            ->select('count(*) as allcount')->count();

            $interestObj = SubInterests::with(array('interests'=> function($query) {
                $query->select('id','interest_name');
            }, 'usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))
            ->where(array('status' => 1))
            ->select('id', 'interest_id', 'sub_interest_name', 'created_at','created_by','status')
            ->whereNotIn('sub_interest_name',['Other'])
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->whereHas('interests', function($query2) use ($searchValue) {
                        $query2->where('interest_name', 'like', '%'.$searchValue.'%');
                    })->OrWhere(function($query3) use ($searchValue) {
                        $query3->whereHas('usersData.business', function ($query4) use ($searchValue) {
                            $query4->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                        })->OrWhereHas('usersData', function ($query5) use ($searchValue) {
                            $query5->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                        });
                    })->OrWhere('sub_interest_name', 'like', '%'.$searchValue.'%');
                });            
            });

            $totalFilteredRows = $interestObj->count();
            $interestData = $interestObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();
          
            $data_arr = [];
            $data_arr = $interestData;
            foreach($interestData as $key => $row) {                  
                $data_arr[$key]->interest_id = !empty($row->interests->interest_name) ? $row->interests->interest_name : '-';
                if ($row->usersData) {
                    $data_arr[$key]->created_by = (($row->usersData->role==3) ? $row->usersData->business->company_name : $row->usersData->user_name) ?? '-';
                }    
                $data_arr[$key]->status = ($row->status == 1) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/interests/sub-interests/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/interests/sub-interests/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/interests/sub-interests/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
                $data_arr[$key]->action = $btn;
            }
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
             );
            echo json_encode($response); exit;

            return Datatables::of($interestData)
                ->addColumn('sub_interest_name', function($row){
                    return $row->sub_interest_name;
                })
                ->addColumn('interest_id', function($row){
                    $interestName = '-';
                    if(!empty($row->interests->interest_name)) {
                        $interestName = $row->interests->interest_name;
                    }
                    return $interestName;
                })
				->addColumn('created_at', function($row){
					return $row->created_at;
				})
                ->addColumn('created_by', function($row){
					return (($row->usersData->role==3) ? $row->usersData->business->company_name : $row->usersData->user_name) ?? '-';
				})
                ->addColumn('status', function($row){
                    $status = '<label class="label label-success">Active</label>';
                    if($row->status == 0){
                        $status = '<label class="label label-danger">Deactive</label>';
                    }
                    return $status;
                })
                ->addColumn('action', function($row){
                    $btn = '';
					$btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/interests/sub-interests/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';

					$btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/interests/sub-interests/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';

					$btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/interests/sub-interests/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';

                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
        }
        return view('superadmin.interests.sub_interests_list',$data);


	}


	/*
		@Author : Spec Developer
		@Desc   : Add interest.
		@Output : \Illuminate\Http\Response
		@Date   : 21/02/2022
	*/
	public function add(){
		$data['page_title'] = 'Add Sub Interest';
		$data['page_js']    = array(
			'backend/assets/superadmin/js/sub_interests.js'
		);
		$data['extra_css'] = array(
			'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
		);
		$data['cdnurl_css'] = array(

		);
		$data['cdnurl_js'] = array(

		);
		$data['extra_js'] = array(
			'plugins/validation/js/jquery.form.js',
			'plugins/validation/js/jquery.validate.min.js',
			'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
		);

		$data['init'] = array(
			'SubInterests.add();'
		);
		$data['interests'] = Interests::select('id','interest_name')->where('status',1)->orderby('interest_name','ASC')->get();
		return view('superadmin.interests.add_sub_interests',$data);
	}
	/*
		@Author : Spec Developer
		@Desc   : Store new sub interest data.
		@Output : \Illuminate\Http\Response
		@Date   : 21/02/2022
	*/

    public function store(Request $request){

       try {
			$validator = Validator::make($request->all(), [
				'interest_name'		=>	'required',
				'sub_interest_name'	=>	'required|unique:sub_interests,sub_interest_name',
			]);

			if ($validator->fails()) {
				Log::info('Add sub interest by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

			$obj = new SubInterests([
                'interest_id'		=> $request->get('interest_name'),
                'sub_interest_name' => trim($request->get('sub_interest_name')),
                'status'			=> $request->get('status'),
                'created_by'        => Auth::user()->id,
            ]);
            $obj->save();

            Alert::success('Success', 'Sub interest has been added!.', 'success');
		    return redirect('superadmin/interests/sub-interests');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/interests/sub-interests/add');
        }
    }

	/*
        @Author : Spec Developer
        @Desc   : Check sub interests name is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 23/02/2022
    */

    public function checkSubInterstExists(Request $request){

        $subInterestId = $request->id;
        if(!empty($subInterestId)){
            $result = SubInterests::where('sub_interest_name', $request->sub_interest_name)->whereNotIn('id', [$subInterestId])->count();
        }
        else
        {
            $result = SubInterests::where('sub_interest_name', $request->sub_interest_name)->count();
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
		@Desc   : Edit sub interest.
		@Output : \Illuminate\Http\Response
		@Date   : 23/02/2022
	*/

    public function editSubInterest($id){

        $data['page_title'] = 'Edit Interest';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/sub_interests.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(

        );
		$data['cdnurl_js'] = array(

        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'SubInterests.edit();'
        );

        // $data['interests'] = Interests::select('id','interest_name')->where('status',1)->orderby('interest_name','ASC')->get();
        $data['interests'] = Interests::select('id','interest_name')->orderby('interest_name','ASC')->get();
		$results	=   SubInterests::where('id',$id)->first();
        $data['data'] = $results;
        return view('superadmin.interests.edit_sub_interests',$data);
    }

	/*
        @Author : Spec Developer
        @Desc   : Update sub interest details.
        @Output : \Illuminate\Http\Response
        @Date   : 23/02/2022
    */

	public function updateSubInterest(Request $request){
        try {
            $id = $request->input('sub_interest_id');

            $validator = Validator::make($request->all(), [
				'interest_name'		=>	'required',
				'sub_interest_name'	=>	'required|unique:sub_interests,sub_interest_name,'.$id,
			]);

			if ($validator->fails()) {
				Log::info('Edit sub interest by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}


            $input['interest_id']   = $request->interest_name;
            $input['sub_interest_name'] = trim($request->sub_interest_name);
            $input['status']        = $request->status;
            $input['updated_by']    = Auth::user()->id;
            //dd($input);
            SubInterests::where('id', $id)->update($input);

            Alert::success('Success', 'Sub interest details updated.', 'success');
		    return redirect('superadmin/interests/sub-interests');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/interests/sub-interests');
        }
	}

	/*
        @Author : Spec Developer
        @Desc   : View sub interest details.
        @Date   : 23/02/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;
		$row['data'] = SubInterests::with(array('interests'=> function($query) {
            $query->select('id','interest_name');
        }, 'usersData'=> function($query) {
            $query->select('id','user_name');
        }))
        ->where(array('status' => 1))
        ->select('id', 'interest_id', 'sub_interest_name', 'created_at','created_by','status')
        ->find($id);

		return view('superadmin.interests.view_sub_interests',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete sub interest record.
        @Output : \Illuminate\Http\Response
        @Date   : 23/02/2022
    */

    public function destroy($id){
		$obj = SubInterests::find($id);
		$obj->delete();
        Alert::success('Success', 'Sub interest has been deleted successfully!', 'success');
		return redirect('superadmin/interests/sub-interests');
    }
}