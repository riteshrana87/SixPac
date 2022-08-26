<?php

namespace App\Http\Controllers\Web\Business;
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
            'backend/assets/business/js/flagged_comment.js'
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

        $flaggedComment = PostComment::with('posts:id,post_title,post_content,user_id')->withCount('commentLike')->withCount('commentDownVote')->withCount('flaggedComment')->with(['user:id,user_name','commentFlagged.flagBy:id,user_name'])
        ->whereHas('posts', function($query) {
            $query->where('user_id', Auth::user()->id);
        })
        ->whereHas('flaggedComment')
        ->withTrashed()
        ->orderby('id','desc')->get();

		if ($request->ajax()) {

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
                        return '<a class="viewUpVotesRecord" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('business/posts/comment/view-comment-upvotes').'" title="View up votes"><span class="badge counter-bg">'.$flaggedComment['comment_like_count'].'</span></a>';
                    }
                })
                ->addColumn('down_vote', function($flaggedComment){
                    if($flaggedComment['comment_down_vote_count'] == 0){
                        return '<span class="badge counter-bg disabled">'.$flaggedComment['comment_down_vote_count'].'</span>';
                    }
                    else
                    {
                        return '<a class="viewDownVotesRecord" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('business/posts/comment/view-comment-downvotes').'" title="View down votes"><span class="badge counter-bg">'.$flaggedComment['comment_down_vote_count'].'</span></a>';
                    }
                })
                ->addColumn('flagged', function($flaggedComment){
                    if($flaggedComment['flagged_comment_count'] == 0){
                        return '<span class="badge flag-counter-bg disabled">'.$flaggedComment['flagged_comment_count'].'</span>';
                    }
                    else
                    {
                        return '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('business/posts/comment/view-comment-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$flaggedComment['flagged_comment_count'].'</span></a>';
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
					$btn .= '<a class="viewCommentRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $flaggedComment['id'] . '" data-url="'.url('business/posts/comments/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                    return $btn;
                })
                ->rawColumns(['link' => true])
                ->make(true);
            }


            return view('business.posts.comments.flagged_list',$data);

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
		return redirect('business/posts/comment/flagged');
    }

    /*
        @Author : Spec Developer
        @Desc   : View comment up vote users details.
        @Date   : 16/05/2022
    */

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
		return view('business.posts.comments.viewVotes',$row);
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
		return view('business.posts.comments.viewVotes',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : View comment down vote users details.
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
		return view('business.posts.comments.viewFlagged',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : View post comment details.
        @Date   : 18/05/2022
    */

    public function view(Request $request){
        $id     =	$request->object_id;
        $row['data'] = PostComment::with('user')->withCount('commentLike')->withCount('commentDownVote')->find($id);
        return view('business.posts.comments.view',$row);
    }

}