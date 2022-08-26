<?php

namespace App\Http\Controllers\Web\SuperAdmin;
use App\Http\Controllers\Controller;

use App\Models\HashTags;
use App\Models\PostComment;
use App\Models\PostGallery;
use App\Models\PostLike;
use App\Models\PostTagTousers;
use App\Models\User;
use App\Models\UserPost;

use App\Http\Resources\LatestPostResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\UserPostResource;
use App\Models\CommentsUpvoteAndDownvote;
use App\Models\CommentTagToUsers;
use App\Models\FlagComment;
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

class FlaggedCommentController extends Controller
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
		@Date   : 19/05/2022
	*/

    public function index(Request $request){
    	$data['page_title'] = 'Flagged Comments';
		$data['page_js'] = array(
            'backend/assets/superadmin/js/flagged_comment.js'
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
            'FlaggedComment.init();'
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

            $totalRecords = PostComment::withCount('commentLike')
            ->withCount('commentDownVote')
            ->withCount('flaggedComment')
            ->with(['user:id,user_name','commentFlagged.flagBy:id,user_name'])
            ->whereHas('flaggedComment')
            ->withTrashed()
            ->select('count(*) as allcount')->count();

            $postCommentObj = PostComment::select('*',DB::raw('deleted_at as status'))
            ->withCount('commentLike')
            ->withCount('commentDownVote')
            ->withCount('flaggedComment')
            ->with(['user:id,user_name,role','commentFlagged.flagBy:id,user_name'])
            ->whereHas('flaggedComment')
            ->withTrashed()
            ->when(!empty($searchValue), function ($query) use ($searchValue) {
                $query->where(function($query1) use ($searchValue) {
                    $query1->where('comment', 'like', '%'.$searchValue.'%')
                    ->OrWhere(function($query2) use ($searchValue) {
                        $query2->whereHas('user.business', function ($query3) use ($searchValue) {
                            $query3->whereRaw(("CASE when role=3 THEN company_name like '%".$searchValue."%' ELSE 0 END"));
                        })->OrWhereHas('user', function ($query4) use ($searchValue) {
                            $query4->whereRaw(("CASE when role!=3 THEN user_name like '%".$searchValue."%' ELSE 0 END"));
                        });
                    });
                });
            });

            $totalFilteredRows = $postCommentObj->count();
            $commentsData = $postCommentObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();
            $data_arr = [];
            $data_arr = $commentsData;
            foreach($commentsData as $key => $row) {
                $totalWords = str_word_count($row->comment);
                if($totalWords > 10){
                    $pieces = explode(" ", $row->comment);
                    $postComment = implode(" ", array_splice($pieces, 0, 10)).' ...';
                } else {
                    $postComment = $row->comment;
                }
                $data_arr[$key]->comment = $postComment ?? '-';
                $data_arr[$key]->comment_like_count = ($row->comment_like_count > 0) ? '<a class="viewUpVotesRecord" href="javascript:void(0)" data-id="'.$row->id.'" data-url="'.url('superadmin/posts/comment/view-comment-upvotes').'" title="View up votes"><span class="badge counter-bg">'.$row->comment_like_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comment_like_count.'</span>';

                $data_arr[$key]->comment_down_vote_count = ($row->comment_down_vote_count > 0) ? '<a class="viewDownVotesRecord" href="javascript:void(0)" data-id="'.$row->id.'" data-url="'.url('superadmin/posts/comment/view-comment-downvotes').'" title="View down votes"><span class="badge counter-bg">'.$row->comment_down_vote_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comment_down_vote_count.'</span>';

                $data_arr[$key]->flagged_comment_count = ($row->flagged_comment_count > 0) ? '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="'.$row->id.'" data-url="'.url('superadmin/posts/comment/view-comment-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$row->flagged_comment_count.'</span></a>' : '<span class="badge flag-counter-bg disabled">'.$row->flagged_comment_count.'</span>';
                if ($row->user) {
                    $data_arr[$key]->user_id = (($row->user->role==3) ? ($row->user->business->company_name ?? '-') : $row->user->user_name) ?? '-';
                }
                $checked = '';
                $flagStatus = '<span class="hide">Deactive</span>';
                if(empty($row->deleted_at)){
                    $checked = 'checked';
                    $flagStatus = '<span class="hide">Active</span>';
                }
                $data_arr[$key]->status = $flagStatus.'<input type="checkbox" name="setStatus" id="status_'.$row->id.'" data-id="'.$row->id.'" data-size="small" data-on-text="Yes" data-off-text="No" data-on-color="success" data-off-color="danger" data-bootstrap-switch class="status_switch" '.$checked.'>';
                $btn = '';
                $btn .= '<a class="viewCommentRecord ml-2 mr-2" href="javascript:void(0)" data-id="'.$row->id . '" data-url="'.url('superadmin/posts/comments/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';               
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
        return view('superadmin.posts.comments.flagged_list',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : Change flagged comment status.
        @Output : \Illuminate\Http\Response
        @Date   : 23/05/2022
    */

    public function changeStatus(Request $request){

        $id = $request->input('comment_id');
		$new_status = $request->input('new_status');

		$validator = $request->validate([
			'comment_id'    =>	'required',
			'new_status'	=>	'required',
		]);

        if($new_status == 0){
            $obj = PostComment::find($id);
            $obj->delete();
        }
        else
        {
            PostComment::withTrashed()->find($id)->restore();
        }

		Alert::success('Success', 'Comment status has been changed successfully!', 'success');
		return redirect('superadmin/posts/comment/flagged');
    }


    public function viewCommentUpVotes(Request $request){

		$id     =	$request->object_id;

        $upVotes = CommentsUpvoteAndDownvote::with('commentUpVote')
             ->with('user')
             ->where('comments_id',$id)
             ->orderby('created_at','desc')
             ->get();


        $row['data'] = array();
        foreach($upVotes as $userRs){
            $userId =  $userRs->user->id;
            $userFirstName =  $userRs->user->name;
            $userName =  $userRs->user->user_name;

            $avtar = !empty($userRs->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$userRs->user->avtar) : asset('backend/assets/images/no-avtar.png');

            $dateDiff = $userRs->created_at->diffForHumans();

            $row['data'][] = '<div class="align-middle m-b-25"><img src="'.$avtar.'" alt="'.$userFirstName.'" class="img-radius align-top m-r-15"><div class="d-inline-block"> <h6 class="postLike-username">'.$userName.'</h6> <span class="status deactive">'.$dateDiff.'</span></div></div>';

        }
		return view('superadmin.posts.comments.viewVotes',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : View comment down vote users details.
        @Date   : 16/05/2022
    */

    public function viewCommentDownVotes(Request $request){

		$id     =	$request->object_id;

        $downVotes = CommentsUpvoteAndDownvote::with('commentDownVote')
             ->with('user')
             ->where('comments_id',$id)
             ->orderby('created_at','desc')
             ->get();

        $row['data'] = array();
        foreach($downVotes as $userRs){
            $userId =  $userRs->user->id;
            $userFirstName =  $userRs->user->name;
            $userName =  $userRs->user->user_name;
            $avtar = !empty($userRs->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$userRs->user->avtar) : asset('backend/assets/images/no-avtar.png');

            $dateDiff = $userRs->created_at->diffForHumans();

            $row['data'][] = '<div class="align-middle m-b-25"><img src="'.$avtar.'" alt="'.$userFirstName.'" class="img-radius align-top m-r-15"><div class="d-inline-block"> <h6 class="postLike-username">'.$userName.'</h6> <span class="status deactive">'.$dateDiff.'</span></div></div>';

        }
		return view('superadmin.posts.comments.viewVotes',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : View post comment details.
        @Date   : 18/05/2022
    */
    public function view(Request $request){
        $id     =	$request->object_id;
        $row['data'] = PostComment::with('user')->withCount('commentLike')->withCount('commentDownVote')->find($id);
        return view('superadmin.posts.comments.view',$row);

    }

    /*
        @Author : Spec Developer
        @Desc   : View comment flagged users details.
        @Date   : 16/05/2022
    */

    public function viewCommentFlagged(Request $request){

		$id     =	$request->object_id;

        $flaggedComment = FlagComment::with('user:id,name,user_name,role,created_at')
             ->where('comment_id',$id)
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
		return view('superadmin.posts.comments.viewFlagged',$row);
    }
}