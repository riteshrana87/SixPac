<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;

use App\Models\HashTags;
use App\Models\FlagComment;
use App\Models\PostGallery;
use App\Models\PostLike;
use App\Models\PostTagTousers;
use App\Models\User;
use App\Models\UserPost;

use App\Http\Resources\LatestPostResource;
use App\Http\Resources\FlagCommentResource;
use App\Http\Resources\UserPostResource;
use App\Models\CommentsUpvoteAndDownvote;
use App\Models\CommentTagToUsers;
use Illuminate\Http\Request;
use DataTables;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlaggedPostController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

	/*
		@Author : Spec Developer
		@Desc   : Fetch flagged comment listing.
		@Output : \Illuminate\Http\Response
		@Date   : 27/05/2022
	*/

    public function index(Request $request){

    	$data['page_title'] = 'Flagged Posts';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/flagged_post.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css',
            'plugins/icheck-bootstrap/icheck-bootstrap.min.css',
			'plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
			'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
        );
		$data['extra_js'] = array(
            'plugins/datatables/jquery.dataTables.min.js',
            'plugins/datatables/dataTables.buttons.min.js',
            'plugins/datatables/jszip.min.js',
            'plugins/datatables/buttons.html5.min.js',
            'plugins/datatables/bootstrap.bundle.min.js',
            'plugins/table/datatable/datatables.js',
			'plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
			'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'FlaggedPost.init();'
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

            $totalRecords = FlagComment::withCount('commentLike')
            ->withCount('commentDownVote')
            ->withCount('flaggedComment')
            ->with(['user:id,user_name','commentFlagged.flagBy:id,user_name'])
            ->withTrashed()
            ->select('count(*) as allcount')->count();

            $commentObj = FlagComment::withCount('commentLike')
            ->withCount('commentDownVote')
            ->withCount('flaggedComment')
            ->with(['user:id,user_name','commentFlagged.flagBy:id,user_name'])
            ->withTrashed()
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->where('post_content', 'like', '%'.$searchValue.'%')
                    ->OrWhere(function($query2) use ($searchValue) {
                        $query2->whereHas('user.business', function ($query3) use ($searchValue) {
                            $query3->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                        })->OrWhereHas('user', function ($query4) use ($searchValue) {
                            $query4->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                        });
                    });
                });
            });

            $totalFilteredRows = $commentObj->count();
            $commentData = $commentObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();

            $data_arr = [];
            $data_arr = $commentData;
            foreach($commentData as $key => $row) {
                $totalWords = str_word_count($row->comment);
                if($totalWords > 10){
                    $pieces = explode(" ", $row->comment);
                    $postContent = implode(" ", array_splice($pieces, 0, 10)).' ...';
                } else {
                    $postContent = $row->comment;
                }
                $data_arr[$key]->comment = $postContent ?? '-';
                $data_arr[$key]->comment_like_count = ($row->comment_like_count > 0) ? '<a class="viewUpVotesRecord" href="javascript:void(0)" data-id="'.$row->id .'" data-url="'.url('superadmin/posts/comment/view-comment-upvotes').'" title="View up votes"><span class="badge counter-bg">'.$row->comment_like_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comment_like_count.'</span>';

                $data_arr[$key]->comment_down_vote_count = ($row->comment_down_vote_count > 0) ? '<a class="viewDownVotesRecord" href="javascript:void(0)" data-id="'.$row->id. '" data-url="'.url('superadmin/posts/comment/view-comment-downvotes').'" title="View down votes"><span class="badge counter-bg">'.$row->comment_down_vote_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comment_down_vote_count.'</span>';

                $data_arr[$key]->flagged_comment_count = ($row->flagged_comment_count > 0) ? '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="'.$row->id.'" data-url="'.url('superadmin/posts/comment/view-comment-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$row->flagged_comment_count.'</span></a>' : '<span class="badge flag-counter-bg disabled">'.$row->flagged_comment_count.'</span>';

                $data_arr[$key]->post_type = ($row->is_public == 0) ? '<i class="fa fa-user private_icon" title="Private"></i>' : '<i class="fa fa-users public_icon" title="Public"></i>';
                if ($row->user) {
                    $data_arr[$key]->user_id = (($row->user->role==3) ? $row->user->business->company_name : $row->user->user_name) ?? '-';
                }
                $checked = '';
                $flagStatus = '<span class="hide">Deactive</span>';
                if(empty($row->deleted_at)){
                    $checked = 'checked';
                    $flagStatus = '<span class="hide">Active</span>';
                }
                $data_arr[$key]->status = $flagStatus.'<input type="checkbox" name="setStatus" id="status_'.$row->id.'" data-id="'.$row->id.'" data-size="small" data-on-text="Yes" data-off-text="No" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="status_switch" '.$checked.'>';
                $btn = '';
                $btn .= '<a class="viewCommentRecord ml-2 mr-2" href="javascript:void(0)" data-id="'.$row->id.'" data-url="'.url('superadmin/posts/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $data_arr[$key]->action = $btn;
            }

            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalFilteredRows,
                "aaData" => $data_arr
             );
           echo json_encode($response); exit;

            return Datatables::of($flaggedComment)
                ->addColumn('id', function($flaggedComment){
                    return $flaggedComment['id'];
                })
                ->addColumn('comment', function($flaggedComment){
                    $totalWords = str_word_count($flaggedComment['comment']);
                    if($totalWords > 10){
                        $pieces = explode(" ", $flaggedComment['comment']);
                        return implode(" ", array_splice($pieces, 0, 10)).' ...';
                    }
                    else
                    {
                        return $flaggedComment['comment'];
                    }
                })
                ->addColumn('up_vote', function($flaggedComment){
                    if($flaggedComment['comment_like_count'] == 0){
                        return '<span class="badge counter-bg disabled">'.$flaggedComment['comment_like_count'].'</span>';
                    }
                    else
                    {
                        return '<a class="viewUpVotesRecord" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('superadmin/posts/comment/view-comment-upvotes').'" title="View up votes"><span class="badge counter-bg">'.$flaggedComment['comment_like_count'].'</span></a>';
                    }
                })
                ->addColumn('down_vote', function($flaggedComment){
                    if($flaggedComment['comment_down_vote_count'] == 0){
                        return '<span class="badge counter-bg disabled">'.$flaggedComment['comment_down_vote_count'].'</span>';
                    }
                    else
                    {
                        return '<a class="viewDownVotesRecord" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('superadmin/posts/comment/view-comment-downvotes').'" title="View down votes"><span class="badge counter-bg">'.$flaggedComment['comment_down_vote_count'].'</span></a>';
                    }
                })
                ->addColumn('flagged', function($flaggedComment){
                    if($flaggedComment['flagged_comment_count'] == 0){
                        return '<span class="badge flag-counter-bg disabled">'.$flaggedComment['flagged_comment_count'].'</span>';
                    }
                    else
                    {
                        return '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('superadmin/posts/comment/view-comment-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$flaggedComment['flagged_comment_count'].'</span></a>';
                    }
                })
				->addColumn('created_by', function($flaggedComment){
                    if(empty($flaggedComment['user']->user_name)){
                        return '-';
                    }
                    else
                    {
                        return $flaggedComment['user']->user_name;
                    }
				})
                ->addColumn('created_at', function($flaggedComment){
					return $flaggedComment['created_at'];
				})
                ->addColumn('status', function($flaggedComment){
                    $checked = '';
					$flagStatus = '<span class="hide">Deactive</span>';
					if(empty($flaggedComment['deleted_at'])){
						$checked = 'checked';
						$flagStatus = '<span class="hide">Active</span>';
					}
					$active = $flagStatus.'<input type="checkbox" name="setStatus" id="status_'.$flaggedComment['id'].'" data-id="'.$flaggedComment['id'].'" data-size="small" data-on-text="Yes" data-off-text="No" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="status_switch" '.$checked.'>';
					return $active;
                })
                ->addColumn('action', function($flaggedComment){
					$btn = '';
					$btn .= '<a class="viewCommentRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('superadmin/posts/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
            }
            return view('superadmin.posts.flagged_list',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : View post flagged users details.
        @Date   : 27/05/2022
    */

    public function viewPostFlagged(Request $request){

		$id     =	$request->object_id;

        $flaggedComment = FlagComment::with('user:id,name,user_name,role,created_at')
             ->where('post_id',$id)
			 ->WhereNull('comment_id')
             ->orderby('created_at','desc')
             ->get();

        $row['data'] = array();

        foreach($flaggedComment as $userRs){
            $userId =  $userRs->user->id;
            $userFirstName =  $userRs->user->name;
            $userName =  $userRs->user->user_name;
            $avtar = !empty($userRs->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$userRs->user->avtar) : asset('backend/assets/images/no-avtar.png');

            $dateDiff = $userRs->created_at->diffForHumans();

            $row['data'][] = '<div class="align-middle m-b-25"><img src="'.$avtar.'" alt="'.$userFirstName.'" class="img-radius align-top m-r-15"><div class="d-inline-block"> <h6 class="postLike-username">'.$userName.'</h6> <span class="status deactive">'.$dateDiff.'</span></div></div>';
        }
		return view('superadmin.posts.viewFlagged',$row);
    }


	/*
        @Author : Spec Developer
        @Desc   : Change flagged post status.
        @Output : \Illuminate\Http\Response
        @Date   : 27/05/2022
    */

    public function changeStatus(Request $request){

        $id = $request->input('comment_id');
		$new_status = $request->input('new_status');

		$validator = $request->validate([
			'comment_id'    =>	'required',
			'new_status'	=>	'required',
		]);

        if($new_status == 0){
            $obj = FlagComment::find($id);
            $obj->delete();
        }
        else
        {
            FlagComment::withTrashed()->find($id)->restore();
        }

		Alert::success('Success', 'Post status has been changed successfully!', 'success');
		return redirect('superadmin/posts/flagged');
    }

}