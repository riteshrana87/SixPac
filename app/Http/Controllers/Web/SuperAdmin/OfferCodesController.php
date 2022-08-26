<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OfferCode;
use Illuminate\Http\Request;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class OfferCodesController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

	/*
		@Author : Spec Developer
		@Desc   : Fetch offer code listing.
		@Output : \Illuminate\Http\Response
		@Date   : 07/03/2022
	*/

    public function index(Request $request){
    	$data['page_title'] = 'Offer Codes';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/offer_codes.js'
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
            'OfferCode.init();'
        );

		if ($request->ajax()) {
            $offerCodeData = OfferCode::with(array('usersData'=> function($query) {
                $query->select('id','name','user_name');
            }))
            ->select('id','offer_code','discount','start_date','end_date','created_at','created_by','status')
            ->orderby('id','DESC')
            ->get();

            return Datatables::of($offerCodeData)
                ->addColumn('offer_code', function($row){
                    return $row->offer_code;
                })
				->addColumn('discount', function($row){
					return $row->discount;
				})
                ->addColumn('start_date', function($row){
					$startDate = explode(' ',$row->start_date);
                    return $startDate[0];
				})
                ->addColumn('end_date', function($row){
					$endDate = explode(' ',$row->end_date);
                    return $endDate[0];
				})
				->addColumn('created_at', function($row){
					return $row->created_at;
				})
				->addColumn('created_by', function($row){
					return $row->usersData->user_name;
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
					$btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/offer-codes/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';

					$btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/offer-codes/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';

					$btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/offer-codes/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';

                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
        }

        return view('superadmin.offer_codes.list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add offer code.
		@Output : \Illuminate\Http\Response
		@Date   : 07/03/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Offer Code';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/offer_codes.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css'
        );
		$data['cdnurl_js'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js'
        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'OfferCode.add();'
        );
        return view('superadmin.offer_codes.add',$data);
	}

    /*
		@Author : Spec Developer
		@Desc   : Store new offer code data.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function store(Request $request){

       try {
			$validator = Validator::make($request->all(), [
				'offer_code'	=>	'required|unique:offer_codes,offer_code',
			]);

			if ($validator->fails()) {
				Log::info('Add offer code by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}
            $startDateTimeStamp = DateTime::createFromFormat('!d/m/Y', $request->input('start_date'))->getTimestamp();
            $endDateTimeStamp   = DateTime::createFromFormat('!d/m/Y', $request->input('end_date'))->getTimestamp();
			$obj = new OfferCode([
                'offer_code'    => trim($request->get('offer_code')),
                'discount'      => trim($request->get('discount')),
                'start_date'    => date('Y-m-d',$startDateTimeStamp),
                'end_date'      => date('Y-m-d',$endDateTimeStamp),
                'created_by'    => Auth::user()->id,
                'status'        => $request->get('status'),
            ]);
            $obj->save();

            Alert::success('Success', 'Offer code has been added!.', 'success');
		    return redirect('superadmin/offer-codes');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/offer-codes/add');
        }
    }

    /*
        @Author : Spec Developer
        @Desc   : Check offer code is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

    public function checkOfferCodeExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = OfferCode::where('offer_code', $request->offer_code)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = OfferCode::where('offer_code', $request->offer_code)->count();
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
		@Desc   : Edit offer code.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/

    public function editOfferCode($id){

        $data['page_title'] = 'Add Offer Code';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/offer_codes.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['cdnurl_css'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css'
        );
		$data['cdnurl_js'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js'
        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['init'] = array(
            'OfferCode.edit();'
        );
        $data['data'] =   OfferCode::where('id',$id)->first();

        return view('superadmin.offer_codes.edit',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Update offer code details.
        @Output : \Illuminate\Http\Response
        @Date   : 23/02/2022
    */

	public function updateOfferCode(Request $request){
        try {
            $id = $request->input('offer_code_id');

            $validator = Validator::make($request->all(), [
				'offer_code'	=>	'required|unique:offer_codes,offer_code,'.$id,
			]);

			if ($validator->fails()) {
				Log::info('Edit offer code by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

            $startDateTimeStamp = DateTime::createFromFormat('!d/m/Y', $request->input('start_date'))->getTimestamp();
            $endDateTimeStamp   = DateTime::createFromFormat('!d/m/Y', $request->input('end_date'))->getTimestamp();

            $input['offer_code']    = $request->offer_code;
            $input['discount']      = $request->discount;
            $input['start_date']    = date('Y-m-d',$startDateTimeStamp);
            $input['end_date']      = date('Y-m-d',$endDateTimeStamp);
            $input['status']        = $request->status;
            //dd($input);
            OfferCode::where('id', $id)->update($input);

            Alert::success('Success', 'Offer code details updated.', 'success');
		    return redirect('superadmin/offer-codes');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/offer-codes');
        }
	}

    /*
        @Author : Spec Developer
        @Desc   : View offer code details.
        @Date   : 23/02/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;

		$row['data'] = OfferCode::with(array('usersData'=> function($query) {
                $query->select('id','name','user_name');
            }))
            ->select('id','offer_code','discount','start_date','end_date','created_at','created_by','status')
            ->orderby('id','DESC')
            ->find($id);

		return view('superadmin.offer_codes.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete offer code record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

    public function destroy($id){
		$obj = OfferCode::find($id);
		$obj->delete();
        Alert::success('Success', 'Offer code has been deleted successfully!', 'success');
		return redirect('superadmin/offer-codes');
    }

}