<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfanityWord;
use Illuminate\Http\Request;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProfanityController extends Controller
{
    public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    /*
		@Author : Spec Developer
		@Desc   : Fetch Profanity word listing.
		@Output : \Illuminate\Http\Response
		@Date   : 27/05/2022
	*/
    public function index(Request $request){
        $data['page_title'] = 'Profanity Words';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/profanity_words.js'
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
            'ProfanityWord.init();'
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

            $totalRecords = ProfanityWord::select('count(*) as allcount')->count();
            $profanityObj = ProfanityWord::select('id','word','created_at','status')
            ->when(!empty($searchValue), function ($query) use ($searchValue) {             
                $query->where('word', 'like', '%'.$searchValue.'%');
            });

            $totalFilteredRows = $profanityObj->count();
            $profanityData = $profanityObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();
            $data_arr = [];
            $data_arr = $profanityData;
            foreach($profanityData as $key => $row) {
                $data_arr[$key]->status = ($row->status == 1) ? '<label class="label label-success">Active</label>' : '<label class="label label-danger">Deactive</label>';
                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('superadmin/profanity-words/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('superadmin/profanity-words/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('superadmin/profanity-words/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
                $data_arr[$key]->action = $btn;
            }
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
             );
            echo json_encode($response); exit;
        }

        return view('superadmin.profanity.list',$data);
	}

    /*
		@Author : Spec Developer
		@Desc   : Add Profanity Word.
		@Output : \Illuminate\Http\Response
		@Date   : 27/05/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Profanity Word';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/profanity_words.js'
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
            'ProfanityWord.add();'
        );
        return view('superadmin.profanity.add',$data);
	}

    /*
		@Author : Spec Developer
		@Desc   : Store new profanity word.
		@Output : \Illuminate\Http\Response
		@Date   : 27/05/2022
	*/
    public function store(Request $request){

       try {
			$validator = Validator::make($request->all(), [
				'profanity_word'	=>	'required|unique:profanity_words,word',
			]);

			if ($validator->fails()) {
				Log::info('Add profanity word by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

			$obj = new ProfanityWord([
                'word'      => trim($request->get('profanity_word')),
                'status'    => $request->get('status'),
            ]);
            $obj->save();

            Alert::success('Success', 'Profanity word has been added!.', 'success');
		    return redirect('superadmin/profanity-words');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/profanity-words/add');
        }
    }

    /*
        @Author : Spec Developer
        @Desc   : Check profanity word is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 27/05/2022
    */

    public function checkWordExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = ProfanityWord::where('word', $request->word)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = ProfanityWord::where('word', $request->word)->count();
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
		@Desc   : Edit profanity word.
		@Output : \Illuminate\Http\Response
		@Date   : 27/05/2022
	*/

    public function editProfanityWord($id){

        $data['page_title'] = 'Add Profanity Word';
        $data['page_js']    = array(
            'backend/assets/superadmin/js/profanity_words.js'
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
            'ProfanityWord.edit();'
        );
        $data['data'] =   ProfanityWord::where('id',$id)->first();

        return view('superadmin.profanity.edit',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Update profanity word details.
        @Output : \Illuminate\Http\Response
       @Date   : 27/05/2022
    */

	public function updateProfanityWord(Request $request){
        try {
            $id = $request->input('word_id');

            $validator = Validator::make($request->all(), [
				'profanity_word'	=>	'required|unique:profanity_words,word,'.$id,
			]);

			if ($validator->fails()) {
				Log::info('Edit profanity word by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

            $input['word']      = $request->profanity_word;
            $input['status']    = $request->status;
            //dd($input);
            ProfanityWord::where('id', $id)->update($input);

            Alert::success('Success', 'Profanity word details updated.', 'success');
		    return redirect('superadmin/profanity-words');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('superadmin/profanity-words');
        }
	}

    /*
        @Author : Spec Developer
        @Desc   : View profanity word details.
        @Date   : 27/05/2022
    */

    public function view(Request $request){
        $id     =	$request->object_id;
        $row['data'] = ProfanityWord::find($id);
        return view('superadmin.profanity.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete profanity word record.
        @Output : \Illuminate\Http\Response
        @Date   : 27/05/2022
    */

    public function destroy($id){
        $obj = ProfanityWord::find($id);
        $obj->delete();
        Alert::success('Success', 'Profanity word has been deleted successfully!', 'success');
        return redirect('superadmin/profanity-words');
    }


}