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
use App\Models\DeviceToken;
use App\Services\FCMService;

class PostCommentController extends Controller
{
	public function __construct()
    {
		$this->middleware('preventBackHistory');
        $this->middleware('auth');

        $this->fileSystemCloud          = Config::get('constant.FILESYSTEM_CLOUD');

        $this->postOriginalImagePath    = Config::get('constant.POST_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->postOrgImageHeight       = Config::get('constant.POST_ORG_PHOTO_HEIGHT');
        $this->postOrgImageWidth        = Config::get('constant.POST_ORG_PHOTO_WIDTH');

        $this->postThumbImagePath       = Config::get('constant.POST_THUMB_PHOTO_UPLOAD_PATH');
        $this->postThumbImageHeight     = Config::get('constant.POST_THUMB_PHOTO_HEIGHT');
        $this->postThumbImageWidth      = Config::get('constant.POST_THUMB_PHOTO_WIDTH');


    }

	/*
		@Author : Spec Developer
		@Desc   : Fetch post comment listing.
		@Output : \Illuminate\Http\Response
		@Date   : 16/03/2022
	*/


    public function index(Request $request){
    	$data['page_title'] = 'Comments';
		$data['page_js'] = array(
            'backend/assets/business/js/post_comment.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css',
            'plugins/autotag/css/jquery.atwho.css',
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
			'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/autotag/js/jquery.caret.js',
            'plugins/autotag/js/jquery.atwho.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'PostComment.init();'
        );

        $totalRecords = PostComment::with('user:id,user_name,role')->withCount('commentLike')->withCount('commentDownVote')->withCount('flaggedComment')->with('posts:id,post_title')->where('post_id',$request->postId)
            ->select('count(*) as allcount')->count();

        if($totalRecords > 0){

		$data['postId'] = $request->postId;

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

            $postCommentObj = PostComment::select('*',DB::raw('deleted_at as status'))
            ->with('user:id,user_name,role')
            ->withCount('commentLike')
            ->withCount('commentDownVote')
            ->withCount('flaggedComment')
            ->with('posts:id,post_title')
            ->where('post_id',$request->postId)
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
                $data_arr[$key]->comment_like_count = ($row->comment_like_count > 0) ? '<a class="viewUpVotesRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/comment/view-comment-upvotes').'" title="View up votes"><span class="badge counter-bg">'.$row->comment_like_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comment_like_count.'</span>';
                 $data_arr[$key]->comment_down_vote_count = ($row->comment_down_vote_count > 0) ? '<a class="viewDownVotesRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/comment/view-comment-downvotes').'" title="View down votes"><span class="badge counter-bg">'.$row->comment_down_vote_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comment_down_vote_count.'</span>';
                  $data_arr[$key]->flagged_comment_count = ($row->flagged_comment_count > 0) ? '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/comment/view-comment-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$row->flagged_comment_count.'</span></a>' : '<span class="badge flag-counter-bg disabled">'.$row->flagged_comment_count.'</span>';
                if ($row->user) {
                    $data_arr[$key]->user_id = (($row->user->role==3) ? $row->user->business->company_name : $row->user->user_name) ?? '-';
                }
                $data_arr[$key]->status = !empty($row->deleted_at) ? '<label class="label label-danger">Deactive</label>' : '<label class="label label-success">Active</label>';
                $btn = '';
                $btn .= '<a class="viewCommentRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/comments/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
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
            return view('business.posts.comments.list',$data);
        }
        else
        {
            Alert::info('Comment not found.',  'No any comment found for this post.', 'info');
		    return redirect('business/posts');
        }
    }

	/*
		@Author : Spec Developer
		@Desc   : Add new comment form modal open
		@Output : \Illuminate\Http\Response
		@Date   : 16/03/2022
	*/
	public function addComment(Request $request){
        $postId	= $request->post_id;
		$row['postId'] = $postId;
		return view('business.posts.comments.add',$row);
	}

	/*
		@Author : Spec Developer
		@Desc   : Save new comment
		@Output : \Illuminate\Http\Response
		@Date   : 16/03/2022
	*/
	public function saveComment(Request $request){
		$comment = $request->comment;
		$data = array();
		try {
            $userPost = UserPost::find($request->post_id);
            if (empty($userPost)) {
                Log::warning('Post not found with id:' .$request->post_id);
				$data['response'] = array('status' => 'error', 'title'=>'Error!', 'message' => trans('admin-message.POST_NOT_FOUND'));
            }
			else
			{
				DB::beginTransaction();

                $userNameArr = array();
                $hashTagsArr = array();

                $newHashTags = "";
                $hashTagNames = array();
                $tagToUsers = array();

                if(!empty($comment)){

                    $regexWithAt = '~(@\w+)~';

                    if (preg_match_all($regexWithAt, $comment, $matches, PREG_PATTERN_ORDER)) {
                    foreach ($matches[1] as $atWord) {
                        $userNameArr[] = str_replace('@','',$atWord);
                    }
                    array_unique($userNameArr);
                    }

                    $regexWithHash = '~(#\w+)~';

                    if (preg_match_all($regexWithHash, $comment, $matches, PREG_PATTERN_ORDER)) {
                    foreach ($matches[1] as $hashWord) {
                        $hashTagsArr[] = str_replace('#','',$hashWord);
                    }
                    array_unique($hashTagsArr);
                    }

                    $hashTagRs = HashTags::select('hash_tag_name')->where('status',1)->get();

                    if(count((array)$hashTagRs) > 0){
                        foreach($hashTagRs as $hasTags){
                            $hashTagNames[] = $hasTags->hash_tag_name;
                        }
                    }

                    if(!empty($hashTagNames) && !empty($hashTagNames)){
                        $newHashTags = array_diff($hashTagsArr,$hashTagNames);
                    }

                    if(!empty($userNameArr)){
                        $usersArr = User::select('id')->whereIn('user_name',$userNameArr)->get();
                        if(count((array) $usersArr) > 0){
                            foreach($usersArr as $users){
                                $tagToUsers[] = $users->id;
                            }
                        }
                    }
                }

				$commentVal = new PostComment;
				$commentVal->comment = $request->comment;
				$commentVal->user_id = Auth::user()->id;
				$commentVal->post_id = $request->post_id;

				$commentVal->user()->associate($request->user());
				$post = UserPost::find($request->post_id);
				$com_data = $post->comments()->save($commentVal);
                $commentId = $com_data->id;

                if(!empty($newHashTags)){
                    if(count((array) $newHashTags) > 0){
                        foreach($newHashTags as $hashTag){
                            $hashTagObj = new HashTags();
                            $hashTagObj->hash_tag_name	= substr($hashTag, 0, 30);
                            $hashTagObj->status	= 1;
                            $hashTagObj->save();
                        }
                    }
                }

                if(!empty($tagToUsers)){
                    if(count((array) $tagToUsers) > 0){
                        foreach($tagToUsers as $userTag){
                            $userTagObj = new CommentTagToUsers();
                            $userTagObj->comment_id = $commentId;
                            $userTagObj->user_id    = $userTag;
                            $userTagObj->save();
                        }
                    }
                }

				DB::commit();
				Log::info('comment added successfully ' . $commentId);

                $deviceTokens = DeviceToken::pluck('device_token')->toArray();
                if (!empty($deviceTokens)) {
                    $notificationData = array(
                            'message' => Auth::user()->user_name . ' has added new comment',
                            'postId' => $commentId
                        );
                    FCMService::send($deviceTokens, $notificationData);
                }

				$data['response'] = array('status' => 'success','title' => 'Success!', 'message' => trans('admin-message.POST_COMMENT_ADDED_SUCCESSFULLY'));
			}
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to comment Post due to err: ' . $e->getMessage());
			$data['response'] = array('status' => 'error', 'title'=>'Error!', 'message' => trans('admin-message.UNABLE_TO_COMMENT_POST'));
        }
		return json_encode($data);
	}

	/*
		@Author : Spec Developer
		@Desc   : Reply on user's comment
		@Output : \Illuminate\Http\Response
		@Date   : 16/03/2022
	*/
    public function commentReplyForm(Request $request){
        $commentId     =	$request->commentId;
        $commentBy     =	$request->commentBy;
        $postId     	=	$request->postId;

		$row['data'] = PostComment::with('posts')->with('usersInfo')->where('id',$commentId)->first();

		return view('business.posts.comments.reply',$row);
	}

	/*
		@Author : Spec Developer
		@Desc   : Save reply on user's comment
		@Output : \Illuminate\Http\Response
		@Date   : 16/03/2022
	*/
	public function saveCommentReply(Request $request){
        $commentId     	=	$request->commentId;
        $commentBy     	=	$request->commentBy;
        $postId     	=	$request->postId;
        $replyComment	=	$request->replyComment;

		try {
            $userPost = UserPost::find($postId);
            if (empty($userPost)) {
                Log::warning('Post not found with id:' . $postId);
            }

            if ($commentId) {
                DB::beginTransaction();
                $reply = new PostComment();
                $reply->comment = $replyComment;
                $reply->user_id = Auth::user()->id;
                $reply->post_id = $postId;
				$reply->user()->associate($request->user());
                $reply->parent_id = $commentId;

				$post = UserPost::find($postId);
                $com_data = $post->comments()->save($reply);

                DB::commit();
                Log::info('comment reply successfully ' . $reply->id);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to comment Post due to err: ' . $e->getMessage());
        }

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
            $userName =  (($userRs->user->role==3) ? $userRs->user->business->company_name : $userRs->user->user_name) ?? '-';
            $role =  $userRs->user->role;

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
            $userName =  (($userRs->user->role==3) ? $userRs->user->business->company_name : $userRs->user->user_name) ?? '-';

            $avtar = !empty($userRs->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$userRs->user->avtar) : asset('backend/assets/images/no-avtar.png');

            $dateDiff = $userRs->created_at->diffForHumans();

            $row['data'][] = '<div class="align-middle m-b-25"><img src="'.$avtar.'" alt="'.$userFirstName.'" class="img-radius align-top m-r-15"><div class="d-inline-block"> <h6 class="postLike-username">'.$userName.'</h6> <span class="status deactive">'.$dateDiff.'</span></div></div>';

        }
		return view('business.posts.comments.viewVotes',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : View post comment details.
        @Date   : 18/05/2022
    */

    public function view(Request $request){
        $id     =	$request->object_id;
        $row['data'] = PostComment::with('user:id,user_name')
        ->withCount('commentLike')
        ->withCount('commentDownVote')
        ->withCount('flaggedComment')
        ->with('postWithTrashed:id,post_title,post_content')
        ->withTrashed()
        ->find($id);
		return view('business.posts.comments.view',$row);
    }

}