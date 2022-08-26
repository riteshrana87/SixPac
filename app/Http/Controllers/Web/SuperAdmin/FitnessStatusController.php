<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FitnessStatus;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class FitnessStatusController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

	/*
		@Author : Spec Developer
		@Desc   : Fetch fitness staus listing.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function index(Request $request){
    	$data['page_title'] = 'Fitness Status';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/fitness_status.js'
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
            'FitnessStatus.init();'
        );

		if ($request->ajax()) {
            $fitnessData = FitnessStatus::select('id','fitness_status','created_at','status')->orderBy('id','DESC')->get();

            return Datatables::of($fitnessData)
                ->addColumn('fitness_status', function($row){
                    return $row->fitness_status;
                })
				->addColumn('created_at', function($row){
					return $row->created_at;
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
					$btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/fitness-status/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';

					$btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/fitness-status/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';

					$btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/fitness-status/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';

                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
        }

        return view('superadmin.fitness_status.list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add fitness status.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Fitness Status';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/fitness_status.js'
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
            'FitnessStatus.add();'
        );
        return view('superadmin.fitness_status.add',$data);
	}

	/*
		@Author : Spec Developer
		@Desc   : Store new fitness status data.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function store(Request $request){

       try {
			$validator = Validator::make($request->all(), [
				'fitness_status'	=>	'required|unique:fitness_statuses,fitness_status',
			]);

			if ($validator->fails()) {
				Log::info('Add fitness status by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

			$obj = new FitnessStatus([
                'fitness_status' => trim($request->get('fitness_status')),
                'status'			=> $request->get('status'),
            ]);
            $obj->save();

            Alert::success('Success', 'Fitness status has been added!.', 'success');
		    return redirect('superadmin/fitness-status');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/fitness-status/add');
        }
    }

	/*
        @Author : Spec Developer
        @Desc   : Check fitness status is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

    public function checkFitnessStatusExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = FitnessStatus::where('fitness_status', $request->fitness_status)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = FitnessStatus::where('fitness_status', $request->fitness_status)->count();
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
		@Desc   : Edit fitness status.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/

    public function editFitnessStatus($id){

        $data['page_title'] = 'Add Fitness Status';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/fitness_status.js'
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
            'FitnessStatus.edit();'
        );
        $data['data'] =   FitnessStatus::where('id',$id)->first();

        return view('superadmin.fitness_status.edit',$data);
    }

	/*
        @Author : Spec Developer
        @Desc   : Update fitness status details.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

	public function updateFitnessStatus(Request $request){

        try {
            $id = $request->input('fitness_status_id');

            $validator = Validator::make($request->all(), [
				'fitness_status'	=>	'required|unique:fitness_statuses,fitness_status,'.$id,
			]);

			if ($validator->fails()) {
				Log::info('Edit fitness status by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

            $input['fitness_status'] = trim($request->fitness_status);
            $input['status']        = $request->status;
            //dd($input);
            FitnessStatus::where('id', $id)->update($input);

            Alert::success('Success', 'Fitness status details updated.', 'success');
		    return redirect('superadmin/fitness-status');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/fitness-status');
        }
	}

	/*
        @Author : Spec Developer
        @Desc   : View fitness status details.
        @Date   : 28/02/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;
		$row['data'] = FitnessStatus::select('id','fitness_status','created_at','status')->find($id);

		return view('superadmin.fitness_status.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete fitness status record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

    public function destroy($id){
		$obj = FitnessStatus::find($id);
		$obj->delete();
        Alert::success('Success', 'Fitness status has been deleted successfully!', 'success');
		return redirect('superadmin/fitness_status');
    }

}