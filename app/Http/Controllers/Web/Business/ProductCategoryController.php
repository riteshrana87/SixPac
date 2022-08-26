<?php

namespace App\Http\Controllers\Web\Business;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProductCategory;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UsersResource;
use App\Services\DataTableService;

class ProductCategoryController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->dataTable = new DataTableService();
    }

    /*
		@Author : Spec Developer
		@Desc   : Fetch product category listing.
		@Output : \Illuminate\Http\Response
		@Date   : 06/03/2022
	*/
    public function index(Request $request){
    	$data['page_title'] = 'Product Category';
		$data['page_js'] = array(
            'backend/assets/business/js/product_category.js'
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
            'ProductCategory.init();'
        );

		if ($request->ajax()) {            
            $searchValue = $request->get('search')['value'] ?? '';
            $query = ProductCategory::with(array('usersData'=> function($query) {
                $query->select('id','user_name','role');
            }))->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->whereHas('usersData.business', function ($query3) use ($searchValue) {
                    $query3->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                })->OrWhereHas('usersData', function ($query4) use ($searchValue) {
                    $query4->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                })->OrWhere('category_name', 'like', '%'.$searchValue.'%');
            });
            $select = ['id','category_name','created_at','created_by','status'];           
            $actions = [
                'view' => url('business/products/product-category/view'),
                'edit' => url('business/products/product-category/edit'),
                'delete' => url('business/products/product-category/destroy/')
            ];
            $this->dataTable->showTable($request,$query,$select,[], $actions);
        }

        return view('business.products.product_category.list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add product category.
		@Output : \Illuminate\Http\Response
		@Date   : 06/03/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Product Category';
        $data['page_js']    = array(
            'backend/assets/business/js/product_category.js'
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
            'ProductCategory.add();'
        );
        return view('business.products.product_category.add',$data);
	}

	/*
		@Author : Spec Developer
		@Desc   : Store new product category data.
		@Output : \Illuminate\Http\Response
		@Date   : 06/03/2022
	*/
    public function store(Request $request){

       try {
			$validator = Validator::make($request->all(), [
				'category_name'	=>	'required|unique:product_categories,category_name',
			]);

			if ($validator->fails()) {
				Log::info('Add product category by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}

			$obj = new ProductCategory([
                'category_name' => trim($request->get('category_name')),
                'created_by'        => Auth::user()->id,
                'status'			=> $request->get('status'),
            ]);
            $obj->save();

            Alert::success('Success', 'Product category has been added!.', 'success');
		    return redirect('business/products/product-category');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/products/product-category/add');
        }
    }

	/*
        @Author : Spec Developer
        @Desc   : Check product category is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 06/03/2022
    */

    public function checkProductCategoryExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = ProductCategory::where('category_name', $request->category_name)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = ProductCategory::where('category_name', $request->category_name)->count();
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
		@Desc   : Edit product category.
		@Output : \Illuminate\Http\Response
		@Date   : 06/03/2022
	*/

    public function editProductCategory($id){

        $data['page_title'] = 'Add Product Category';
        $data['page_js']    = array(
            'backend/assets/business/js/product_category.js'
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
            'ProductCategory.edit();'
        );
        $data['data'] =   ProductCategory::where('id',$id)->first();

        return view('business.products.product_category.edit',$data);
    }

	/*
        @Author : Spec Developer
        @Desc   : Update product category details.
        @Output : \Illuminate\Http\Response
        @Date   : 06/03/2022
    */

	public function updateProductCategory(Request $request){

        try {
            $id = $request->input('category_id');

            $validator = Validator::make($request->all(), [
				'category_name'	=>	'required|unique:product_categories,category_name,'.$id,
			]);

			if ($validator->fails()) {
				Log::info('Edit product category by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
			}
            $input['category_name'] = trim($request->category_name);
            $input['status']        = $request->status;
            //dd($input);
            ProductCategory::where('id', $id)->update($input);

            Alert::success('Success', 'Product category details updated.', 'success');
		    return redirect('business/products/product-category');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/products/product-category');
        }
	}

	/*
        @Author : Spec Developer
        @Desc   : View product category details.
        @Date   : 06/03/2022
    */

    public function view(Request $request){
		$id     =	$request->object_id;
		//$row['data'] = ProductCategory::select('id','category_name','created_at','status')->find($id);
		$row['data'] = ProductCategory::with(array('usersData'=> function($query) {
                $query->select('id','user_name');
            }))
            ->where(array('status' => 1))
            ->select('id','category_name','created_at','created_by','status')
            ->orderby('id','DESC')
            ->find($id);

		return view('business.products.product_category.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete product category record.
        @Output : \Illuminate\Http\Response
        @Date   : 06/03/2022
    */

    public function destroy($id){
		$obj = ProductCategory::find($id);
		$obj->delete();
        Alert::success('Success', 'Product category has been deleted successfully!', 'success');
		return redirect('business/products/product-category');
    }
}