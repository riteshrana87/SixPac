<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use DB;

class DataTableService
{
    public function __construct()
    {
    }

    /**
     * prepareData is used to prepare data for dataTable
     *
     * @param  object $result  result data array
     * @param  array  $actions actions that you want to show
     * @return array  return data array
     * @author Spec Developer
     */
    public function prepareData($result, $actions, $fullAccess) {
        $response = $result;
        foreach($result as $key => $row) {
            if (isset($row->phone)) {                
                $response[$key]->phone = !empty($row->phone) ? convertPhoneToUsFormat($row->phone) : '-';
            }
            if (isset($row->status)) {               
                if (!empty($fullAccess)) {
                    $response[$key]->status = (($row->status == 1) ? '<span class="hide">Active</span>' : '<span class="hide">Deactive</span>').'<input type="checkbox" name="setStatus" id="status_'.$row->id.'" data-id="'.$row->id.'" data-size="small" data-on-text="Yes" data-off-text="No" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="status_switch" '.($row->status == 1 ? 'checked' : '').'>';
                } else {
                    $response[$key]->status = ($row->status == 0) ? '<label class="label label-danger">Deactive</label>' : '<label class="label label-success">Active</label>';
                }
            }
            if (!empty($actions)) {
                $btn = '';
                if (!empty($actions['view'])) {
                    $btn .= ($row->role == 1) ? null : '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.$actions['view'].'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                }
                if (!empty($actions['edit'])) {
                    $btn .= ($row->role == 1) ? null : '<a class="editRecord ml-2 mr-2" href="'.$actions['edit'].'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                }
                if (!empty($actions['delete'])) {
                    $btn .= ($row->role == 1) ? null : '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.$actions['delete'].'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
                }
                $response[$key]->action = $btn;
            }
            if ($row->usersData && isset($response[$key]->created_by)) {
                $response[$key]->created_by = (($row->usersData->role==3) ? ($row->usersData->business->company_name ?? '-') : $row->usersData->user_name) ?? '-';
            }        
        }
        return $response;

    }

    /**
     * prepareQuery is used to prepare dynamic query for datatable
     *
     * @param  Request $request request parameter
     * @param  string  $table      table name
     * @param  array   $select     select fields
     * @param  array   $search     search fields
     * @param  array   $actions    action you want to show
     * @param  int     $fullAccess 1 if full access otherwise 0
     * @return json    return json data
     * @author Spec Developer
     */
    public function prepareQuery(Request $request, $query, $select = ['*'], $search, $actions, $fullAccess) {
        try {
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

            $totalRecords = $query->select('count(*) as allcount')->count();
            $tableObj = $query->select($select)
            ->when(!empty($search), function ($q) use ($search, $searchValue) {
                $q->where(function ($query) use ($search, $searchValue) {
                    foreach ($search as $col) {
                        $query->OrWhere($col, 'like', '%' .$searchValue . '%');
                    }
                });
            });
            $totalFilteredRows = $tableObj->count();

            $result = $tableObj->skip($start)
                    ->take($rowPerPage)
                    ->orderBy($columnName, $sortOrder)
                    ->get();                    
            return [
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $this->prepareData($result, $actions, $fullAccess) ?? []
            ];
        } catch (\Exception $e) {
            return [
                "draw" => 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ];
        }
    }

    /**
     * showTable is used to show yajra dataTable
     *
     * @param  Request $request request parameter
     * @param  string  $table      table name
     * @param  array   $select     select fields
     * @param  array   $search     search fields
     * @param  array   $actions    action you want to show
     * @param  int     $fullAccess 1 if full access otherwise 0
     * @return json    return json data
     * @author Spec Developer
     */
    public function showTable(Request $request, $query, array $select = ['*'], $search = [], $actions = [], $fullAccess = 0)
    {
        try {
            $response = $this->prepareQuery($request, $query,$select, $search, $actions, $fullAccess);
        } catch (\Exception $e) {
            $response = [
                "draw" => 0,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            ];
       }
       echo json_encode($response); exit;
    }        
}
