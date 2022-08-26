<?php

namespace App\Http\Controllers\Web\Business;
use App\Http\Controllers\Controller;

use App\Http\Resources\LatestPostResource;
use App\Http\Resources\PostCommentResource;
use App\Http\Resources\UserPostResource;
use App\Models\CommentsUpvoteAndDownvote;

use App\Models\HashTags;
use App\Models\PostComment;
use App\Models\PostGallery;
use App\Models\PostLike;
use App\Models\PostTagTousers;
use App\Models\User;
use App\Models\UserPost;
use App\Models\UsersTagsToPost;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Services\ImageUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use DateTime;
use File;
use getID3;
use App\Models\DeviceToken;
use App\Services\FCMService;

class PostsController extends Controller
{

	public function __construct(){
		$this->middleware('preventBackHistory');
        $this->middleware('auth');
        $this->fileSystemCloud          = Config::get('constant.FILESYSTEM_CLOUD');

        $this->postOriginalImagePath    = Config::get('constant.POST_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->postOrgImageHeight       = Config::get('constant.POST_ORG_PHOTO_HEIGHT');
        $this->postOrgImageWidth        = Config::get('constant.POST_ORG_PHOTO_WIDTH');

        $this->postThumbImagePath       = Config::get('constant.POST_THUMB_PHOTO_UPLOAD_PATH');
        $this->postThumbImageHeight     = Config::get('constant.POST_THUMB_PHOTO_HEIGHT');
        $this->postThumbImageWidth      = Config::get('constant.POST_THUMB_PHOTO_WIDTH');

        $this->userOriginalImagePath    = Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH');
        $this->userThumbImagePath       = Config::get('constant.USER_THUMB_PHOTO_UPLOAD_PATH');
        $this->userThumbImageHeight     = Config::get('constant.USER_THUMB_PHOTO_HEIGHT');
        $this->userThumbImageWidth      = Config::get('constant.USER_THUMB_PHOTO_WIDTH');
    }

	/*
		@Author : Spec Developer
		@Desc   : Fetch post listing.
		@Output : \Illuminate\Http\Response
		@Date   : 08/03/2022
	*/

    public function index(Request $request){
    	$data['page_title'] = 'Posts';
		$data['page_js'] = array(
            'backend/assets/business/js/posts.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css',
            'plugins/table/datatable/datatables.css',
            'plugins/fancybox/dist/jquery.fancybox.min.css',
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
            'plugins/table/datatable/datatables.js',
            'plugins/fancybox/dist/jquery.fancybox.min.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'Posts.init();'
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
            $totalRecords = UserPost::with('user:id,user_name,role')
            ->withCount('comments')
            ->withCount('likes')
            ->withCount('flaggedPost')
            ->where('user_id', Auth::user()->id)
            ->select('count(*) as allcount')->count();

            $postObj = UserPost::with('user:id,user_name,role')
            ->withCount('comments')
            ->withCount('likes')
            ->withCount('flaggedPost')
            ->where('user_id', Auth::user()->id)
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

            $totalFilteredRows = $postObj->count();

            $postData = $postObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();

            $data_arr = [];
            $data_arr = $postData;
            foreach($postData as $key => $row) {
                $totalWords = str_word_count($row->post_content);
                if($totalWords > 10){
                    $pieces = explode(" ", $row->post_content);
                    $postContent = implode(" ", array_splice($pieces, 0, 10)).' ...';
                } else {
                    $postContent = $row->post_content;
                }
                $data_arr[$key]->post_content = $postContent ?? '-';
                $data_arr[$key]->comments_count = ($row->comments_count > 0) ? '<a href="'.url('business/posts/comments/'.$row->id).'"><span class="badge counter-bg">'.$row->comments_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comments_count.'</span>';

                $data_arr[$key]->likes_count = ($row->likes_count > 0) ? '<a class="viewLikesRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/view-post-likes').'" title="View"><span class="badge counter-bg">'.$row->likes_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->likes_count.'</span>';

                $data_arr[$key]->flagged = ($row->flagged_post_count > 0) ? '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/view-post-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$row->flagged_post_count.'</span></a>' : '<span class="badge flag-counter-bg disabled">'.$row->flagged_post_count.'</span>';

                $data_arr[$key]->post_type = ($row->is_public == 0) ? '<i class="fa fa-user private_icon" title="Private"></i>' : '<i class="fa fa-users public_icon" title="Public"></i>';

                if ($row->user) {
                    $data_arr[$key]->user_id = (($row->user->role==3) ? $row->user->business->company_name : $row->user->user_name) ?? '-';
                }               
                $data_arr[$key]->status = ($row->status == 0) ? '<label class="label label-danger">Deactive</label>' : '<label class="label label-success">Active</label>';

                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
                $btn .= '<a class="editRecord ml-2 mr-2" href="'.url('business/posts/edit').'/'.$row->id.'" title="Edit"><i class="fa fa-pencil fa-action-edit"></i></a>';
                $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/posts/destroy/').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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

        return view('business.posts.list',$data);
    }

	/*
		@Author : Spec Developer
		@Desc   : Add Posts.
		@Output : \Illuminate\Http\Response
		@Date   : 08/03/2022
	*/
    public function add(){
		$data['page_title'] = 'Add Post';
        $data['page_js']    = array(
            'backend/assets/business/js/add_posts.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
            'plugins/autotag/css/jquery.atwho.css',
        );
		$data['cdnurl_css'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css'
        );
		$data['cdnurl_js'] = array(
            '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js',
        );
		$data['extra_js'] = array(
            'plugins/validation/js/jquery.form.js',
            'plugins/validation/js/jquery.validate.min.js',
            'plugins/bootstrap-switch/custom/js/bootstrap-switch.js',
            'plugins/autotag/js/jquery.caret.js',
            'plugins/autotag/js/jquery.atwho.js',
        );

		$data['init'] = array(
            'Posts.add();'
        );
        return view('business.posts.add',$data);
	}

    /*
		@Author : Spec Developer
		@Desc   : Store new post data.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/
    public function store(Request $request){

       try {
            // dd($request->all());
            $post_title	    = trim($request->get('post_title'));
			$post_slug		= Str::random(20);
			$post_content	= $request->get('post_content');
            $notes	        = trim($request->get('notes'));
            $is_public		= $request->get('is_public');
            $status			= $request->get('status');
            $video_thumb    = $request->input('video_thumb');

            $fileValidationArr = array();

            $textValidationArr = array(
                // 'post_title'	=>	'sometimes|unique:user_posts,post_title',
                'post_content'	=>	'required',
            );
            $validationArr = array_merge($textValidationArr,$fileValidationArr);

            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Add post by super admin :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $userNameArr = array();
            $hashTagsArr = array();

            $newHashTags = "";
            $hashTagNames = array();
            $tagToUsers = array();

			if(!empty($post_content)){

				$regexWithAt = '~(@\w+)~';

				if (preg_match_all($regexWithAt, $post_content, $matches, PREG_PATTERN_ORDER)) {
				   foreach ($matches[1] as $atWord) {
					  $userNameArr[] = str_replace('@','',$atWord);
				   }
                   array_unique($userNameArr);
				}

				$regexWithHash = '~(#\w+)~';

				if (preg_match_all($regexWithHash, $post_content, $matches, PREG_PATTERN_ORDER)) {
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


			$post = new UserPost();
            $post->user_id		=   Auth::user()->id;
            $post->post_title   =   $post_title;
            $post->post_slug	=   $post_slug;
            $post->post_content =   $post_content;
            $post->notes        =   $notes;
            $post->status		=   $status;
            // $post->is_public	=   $is_public;
            $post->is_public	=   1;
            $post->save();
			$postId = $post->id;

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
                        $userTagObj = new PostTagTousers();
                        $userTagObj->post_id    = $postId;
                        $userTagObj->user_id    = $userTag;
                        $userTagObj->save();
                    }
                }
            }

            $thumbArr = [];
            if(count((array) $request->mediaThumb) > 0){
                foreach ($request->mediaThumb as $key => $thumb) {
                    $base64_str = substr($thumb, strpos($thumb, ",")+1);
                    $file = base64_decode($base64_str);
                    $thumbArr[$key] = $file;
                }
            } 
            if(count((array) $request->media) > 0){
                $i = 1;
				foreach ($request->media as $key => $gallery) {
					$base64_str = substr($gallery, strpos($gallery, ",")+1);
					$file = base64_decode($base64_str);
					$extension = strtolower(explode('/', explode(':', substr($gallery, 0, strpos($gallery, ';')))[1])[1]);
					// $fileName = Str::random(10).'.'.$extension;
                    $mediaExtArr = Config::get('constant.MEDIA_EXTENSION');
                    if(!empty($mediaExtArr[$extension])){
                        $extension = $mediaExtArr[$extension];
                    }

                    $fileName = $key.'_'.$postId.'.'.$extension;
                    $thumbFileName = $key.'_'.$postId.'_thumb.'.$extension;

					Storage::disk('public')->put($this->postOriginalImagePath.$fileName, $file);

                    if (!empty($thumbArr) && array_key_exists($key, $thumbArr)) {
                        Storage::disk($this->fileSystemCloud)->put($this->postThumbImagePath.$thumbFileName, $thumbArr[$key]);
                    } else {
    					if(in_array($extension, Config::get('constant.IMAGE_EXTENSION'))){
                            $originalPath = Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath.$fileName);
                            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->postThumbImagePath.$thumbFileName);

                            $img = Image::make($originalPath); // Open an image file
                            $img->resize($this->postThumbImageWidth, $this->postThumbImageHeight); // Resize image
                            $img->save($thumbPath); // Save file into destination folder
    					}
                    }
					$fileLength = 0;
					if(in_array($extension, Config::get('constant.VIDEO_EXTENSION'))){
                    // if($extension == 'video/mp4' || $extension == 'mp4'){
						$getID3 = new getID3;
						$videoPath = Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath.$fileName);
						$video_file = $getID3->analyze($videoPath);
						// $duration_string = $video_file['playtime_string'];	// Get the duration in string
						$fileLength = $video_file['playtime_seconds']; // Get the duration in seconds

                        if(!empty($video_thumb)){

                            $thumbUrl    =   $video_thumb[$key];

                            $base64_str = substr($thumbUrl, strpos($thumbUrl, ",")+1);
                            $file       = base64_decode($base64_str);
                            $thumbExtension  = strtolower(explode('/', explode(':', substr($thumbUrl, 0, strpos($thumbUrl, ';')))[1])[1]);
                            // $fileName = Str::random(10).'.'.$extension;

                            $thumbFileName = $key.'_'.$postId.'_thumb.'.$thumbExtension;

                            Storage::disk('public')->put($this->postThumbImagePath.$thumbFileName, $file);

                            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->postThumbImagePath.$thumbFileName);
                            $img = Image::make($thumbPath); // Open an image file
                            $img->resize($this->postThumbImageWidth, $this->postThumbImageHeight); // Resize image
                            $img->save($thumbPath);
                        }
					}

					$file_original_url = Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath.$fileName);
					$fileSize = filesize($file_original_url);

					$fileObj = new PostGallery([
						'post_id'       =>  $postId,
						'file_name'     =>  $fileName,
                        'media_order'   =>  $i,
                        'thumb_name'    =>  $thumbFileName,
						'file_type'     =>  $extension,
						'file_length'   =>  $fileLength,
						'file_size'     =>  $fileSize,
						'is_transacoded'=>  'transacoded',
						'status'        =>  1,
					]);
					$fileObj->save();
                    $i++;
				}
			}
            $deviceTokens = DeviceToken::pluck('device_token')->toArray();
            if (!empty($deviceTokens)) {
                $notificationData = array(
                        'message' => Auth::user()->user_name . ' has added new post',
                        'postId' => $fileObj->id
                    );
                FCMService::send($deviceTokens, $notificationData);
            }

            Alert::success('Success', 'Post has been added!.', 'success');
		    return redirect('business/posts');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/posts/add');
        }
    }

    /*
        @Author : Spec Developer
        @Desc   : Check post is already exists?.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

    public function checkPostExists(Request $request){
        $id = $request->id;
        if(!empty($id)){
            $result = UserPost::where('post_title', $request->post_title)->whereNotIn('id', [$id])->count();
        }
        else
        {
            $result = UserPost::where('post_title', $request->post_title)->count();
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
		@Desc   : Edit Posts.
		@Output : \Illuminate\Http\Response
		@Date   : 28/02/2022
	*/

    public function editPost($id){

        $data['page_title'] = 'Edit Post';
        $data['page_js']    = array(
            'backend/assets/business/js/edit_posts.js'
        );
        $data['extra_css'] = array(
            'plugins/bootstrap-switch/custom/css/bootstrap-switch.css',
            'plugins/autotag/css/jquery.atwho.css',
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
            'plugins/autotag/js/jquery.caret.js',
            'plugins/autotag/js/jquery.atwho.js',
        );

		$data['init'] = array(
            'Posts.edit();'
        );

        $data['data'] = UserPost::where('id',$id)->first();

        $galleryRs = PostGallery::select('id','file_name','media_order','thumb_name','file_type','file_length','file_size')->where('post_id',$id)->orderby('media_order')->get();
		$data['postGallery'] = array();


        $x = 0;
		if(count((array)$galleryRs) > 0){
			foreach($galleryRs as $gallery){
				$data['postGallery']['file_url'][$x]    = !empty($gallery->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->postOriginalImagePath.$gallery->file_name) : asset('backend/assets/images/no-post.png');
				$data['postGallery']['gallery_id'][$x]  = $gallery->id;
				$data['postGallery']['file_name'][$x]   = $gallery->file_name;
                $data['postGallery']['thumb_url'][$x]    = !empty($gallery->thumb_name) ? Storage::disk($this->fileSystemCloud)->url($this->postThumbImagePath.$gallery->thumb_name) : asset('backend/assets/images/no-post.png');
				$data['postGallery']['file_type'][$x]   = $gallery->file_type;
				$data['postGallery']['file_length'][$x] = $gallery->file_length;
				$data['postGallery']['file_size'][$x]   = $gallery->file_size;
                $data['postGallery']['media_order'][$x]   = $gallery->media_order;
                if (in_array($gallery->file_type, Config::get('constant.IMAGE_EXTENSION'))) {
                    $data['postGallery']['original_image'][$x]    = !empty($gallery->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->postOriginalImagePath.$gallery->file_name) : asset('backend/assets/images/no-post.png');
                }
				$x++;
			}
		}
		$data['totalMedia'] = $x;

        $taggedUsers = PostTagTousers::select('user_id')->where('post_id', $id)->get();
        $tagedUsersId = array();
        $data['old_tag_users'] = '';
        if(!empty($taggedUsers) && count((array) $taggedUsers) > 0){
            foreach($taggedUsers as $userIds){
                $tagedUsersId[] = $userIds->user_id;
            }
            $data['old_tag_users'] = implode(',',$tagedUsersId);
        }
        return view('business.posts.edit',$data);
    }

    /**
     * getPostGallery is used to get post gallery id
     *
     * @param  int $postId    post id
     * @param  int $fileCount number of files count
     * @return mix            file index if found othrwise false
     * @author Spec Developer
     */
    public function getPostGallery($postId, $fileCount){
        $galleries = PostGallery::where('post_id', $postId)->pluck('file_name')->toArray();
        if (!empty($galleries)) {
            $oldGalleryIds = [];
            foreach($galleries as $gallery){
                $media = explode('_',$gallery);
                array_push($oldGalleryIds, $media[0]);
            }
            $totalCount = count($galleries) + $fileCount;
            for($i=1; $i<=$totalCount; $i++){
                if (!in_array($i, $oldGalleryIds)) {
                    return $i;
                }
            }
        }
        return false;
    }

    /*
        @Author : Spec Developer
        @Desc   : Update post details.
        @Output : \Illuminate\Http\Response
        @Date   : 23/02/2022
    */

	public function updatePost(Request $request){
        try {            
            // dd($request->all());
            $id = $request->input('post_id');
            $post_title	    = trim($request->input('post_title'));
            $post_content	= trim($request->input('post_content'));
            $notes		    = $request->input('notes');
            $is_public		= $request->input('is_public');
            $status			= $request->input('status');
            $oldUserIdsArr	= array($request->input('old_tag_users'));
            $video_thumb    = $request->input('video_thumb');
            $oldMediaOrders = $request->input('oldMediaOrder');
            // $oldUserIdsArr = array();
            // if(!empty($oldUsersTags)){
            //     $oldUserIdsArr = explode(',',$oldUsersTags);
            // }

            $fileValidationArr = array();

            $textValidationArr = array(
                // 'post_title'	=>	'sometimes|unique:user_posts,post_title,'.$id,
                'post_content'	=>	'required',
            );
            $validationArr = array_merge($textValidationArr,$fileValidationArr);

            $validator = Validator::make($request->all(),$validationArr);
            if ($validator->fails()) {
                Log::info('Edit post by business user :: message :' . $validator->errors());
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $gallery = array();

            // Upload User Photo
            $oldMediaArr	= $request->input('old_media');

			$galleryArr = PostGallery::select('id')->where('post_id',$id)->get();
			$oldMediaIds = array();

			if(count($galleryArr) > 0){
				foreach($galleryArr as $mediaArr){
					$oldMediaIds[] = $mediaArr->id;
				}
			}

            $originalPath	= Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath);
            $thumbPath		= Storage::disk($this->fileSystemCloud)->path($this->postThumbImagePath);

            if(isset($oldMediaArr) && !empty($oldMediaArr)){
				$delGallleryArr = array_diff($oldMediaIds, $oldMediaArr);

				if(count((array)$delGallleryArr) > 0){
					foreach($delGallleryArr as $mediaId){
						deletePostMediaFromMediaId($mediaId, $originalPath, $thumbPath, 0);
					}
				}
			}

            if(count((array)$oldMediaArr) == 0){
                deletePostMediaFromPostId($id, $originalPath, $thumbPath, 0);
            }

            $userNameArr = array();
            $hashTagsArr = array();

            $newHashTags = "";
            $hashTagNames = array();
            $tagToUsers = array();

			if(!empty($post_content)){

				$regexWithAt = '~(@\w+)~';

				if (preg_match_all($regexWithAt, $post_content, $matches, PREG_PATTERN_ORDER)) {
				   foreach ($matches[1] as $atWord) {
					  $userNameArr[] = str_replace('@','',$atWord);
				   }
                   array_unique($userNameArr);
				}

				$regexWithHash = '~(#\w+)~';

				if (preg_match_all($regexWithHash, $post_content, $matches, PREG_PATTERN_ORDER)) {
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

                $commonUserIdsArr = array_intersect($oldUserIdsArr, $tagToUsers); // find common users id
                $removeUsersIdArr = array_diff($oldUserIdsArr, $commonUserIdsArr); // find delete tag users id

                /*Remove other values (delete query) */
                if(!empty($removeUsersIdArr)){
                    PostTagTousers::where('post_id', $id)->whereIn('user_id', $removeUsersIdArr)->delete();
                }

                $tagToUsers = array_diff($tagToUsers, $commonUserIdsArr);

			}

            // Upload post video
            // $input['post_title']    = trim($request->post_title);
            $input['post_title']    = $post_title;
            $input['post_content']  = $post_content;
            $input['notes']         = $notes;
            // $input['is_public']     = $is_public;
            $input['status']        = $status;
            //dd($input);
            UserPost::where('id', $id)->update($input);

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
                        $userTagObj = new PostTagTousers();
                        $userTagObj->post_id    = $id;
                        $userTagObj->user_id    = $userTag;
                        $userTagObj->save();
                    }
                }
            }

            if (!empty($oldMediaOrders)) {
                foreach ($oldMediaOrders as $galleryId => $mediaOrder) {
                    PostGallery::where('id', $galleryId)->update(['media_order' => (int)$mediaOrder]);
                }
            }

            if (count((array) $request->oldThumb) > 0) {
                foreach ($request->oldThumb as $galleryId => $thumb) {
                    $base64_str = substr($thumb, strpos($thumb, ",")+1);
                    $file = base64_decode($base64_str);                    
                    $gallery = PostGallery::where('id', $galleryId)->first();
                    if ($gallery) {
                        $filePath = $this->postThumbImagePath.$gallery->thumb_name;
                        if (Storage::disk($this->fileSystemCloud)->exists($filePath)) {
                            Storage::disk($this->fileSystemCloud)->delete($filePath);
                            Storage::disk($this->fileSystemCloud)->put($filePath, $file);
                        }
                    }
                }
            }

            $thumbArr = [];
            if (count((array) $request->newThumb) > 0) {
                foreach ($request->newThumb as $key => $thumb) {
                    $base64_str = substr($thumb, strpos($thumb, ",")+1);
                    $file = base64_decode($base64_str);
                    $thumbArr[$key] = $file;
                }
            }
            // dd($thumbArr);
            /** Upload images/video code start here **/
			if(count((array)$request->get('media')) > 0){
				foreach (request()->media as $key => $gallery) {
					$base64_str = substr($gallery, strpos($gallery, ",")+1);
					$file = base64_decode($base64_str);
					$extension = strtolower(explode('/', explode(':', substr($gallery, 0, strpos($gallery, ';')))[1])[1]);

                    $mediaExtArr = Config::get('constant.MEDIA_EXTENSION');
                    if(!empty($mediaExtArr[$extension])){
                        $extension = $mediaExtArr[$extension];
                    }
                    $fileCount = count((array)$request->get('media'));
                    $fileIndex = $this->getPostGallery($id, $fileCount);
                    $fileName = $fileIndex.'_'.$id.'.'.$extension;
                    $thumbFileName = $fileIndex.'_'.$id.'_thumb.'.$extension;

					Storage::disk('public')->put($this->postOriginalImagePath.$fileName, $file);
                    if (!empty($thumbArr) && array_key_exists($key, $thumbArr)) {
                        Storage::disk($this->fileSystemCloud)->put($this->postThumbImagePath.$thumbFileName, $thumbArr[$key]);
                    } else {
    					if(in_array($extension, Config::get('constant.IMAGE_EXTENSION'))){
                            $originalPath = Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath.$fileName);
                            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->postThumbImagePath.$thumbFileName);

                            $img = Image::make($originalPath); // Open an image file
                            $img->resize($this->postThumbImageWidth, $this->postThumbImageHeight); // Resize image
                            $img->save($thumbPath); // Save file into destination folder
    					}
                    }
					$fileLength = 0;
                    if(in_array($extension, Config::get('constant.VIDEO_EXTENSION'))){
						$getID3 = new getID3;
						$videoPath = Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath.$fileName);
						$video_file = $getID3->analyze($videoPath);
						// $duration_string = $video_file['playtime_string'];	// Get the duration in string
						$fileLength = $video_file['playtime_seconds']; // Get the duration in seconds

                        if(!empty($video_thumb)){

                            $thumbUrl    =   $video_thumb[$key];

                            $base64_str = substr($thumbUrl, strpos($thumbUrl, ",")+1);
                            $file       = base64_decode($base64_str);
                            $thumbExtension  = strtolower(explode('/', explode(':', substr($thumbUrl, 0, strpos($thumbUrl, ';')))[1])[1]);
                            // $fileName = Str::random(10).'.'.$extension;
                            $thumbFileName = $key.'_'.$id.'_thumb.'.$thumbExtension;

                            Storage::disk('public')->put($this->postThumbImagePath.$thumbFileName, $file);

                            $thumbPath = Storage::disk($this->fileSystemCloud)->path($this->postThumbImagePath.$thumbFileName);
                            $img = Image::make($thumbPath); // Open an image file
                            $img->resize($this->postThumbImageWidth, $this->postThumbImageHeight); // Resize image
                            $img->save($thumbPath);
                        }
					}

					$file_original_url = Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath.$fileName);
					$fileSize = filesize($file_original_url);

					$fileObj = new PostGallery([
						'post_id'       =>  $id,
						'file_name'     =>  $fileName,
                        'thumb_name'    =>  $thumbFileName,
                        'media_order'   =>  $key,
						'file_type'     =>  $extension,
						'file_length'   =>  $fileLength,
						'file_size'     =>  $fileSize,
						'is_transacoded'=>  'transacoded',
						'status'        =>  1,
					]);
					$fileObj->save();
				}
			}

			/** Upload images/video code end here **/

            Alert::success('Success', 'Post details updated.', 'success');
		    return redirect('business/posts');
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            Alert::error('Error',  $e->getMessage(), 'error');
		    return redirect('business/posts');
        }
	}


    /*
        @Author : Spec Developer
        @Desc   : View post details.
        @Date   : 23/02/2022
    */

    public function view(Request $request){

		$id     =	$request->object_id;

		$row['data'] = UserPost::with('user:id,user_name,role')
        ->withCount('postMediaRec')
        ->with('postMediaRec:id,post_id,file_name,file_type,thumb_name')
        ->withCount('comments')
        ->withCount('likes')
        ->withCount('flaggedPost')
        ->orderby('id','desc')
        ->find($id);

        $mediaArr = array();
        $x = 0;
        if($row['data']->post_media_rec_count > 0){
            foreach($row['data']->postMediaRec as $media){
                $mediaArr[$x]['id'] = $media->id;
                $file = !empty($media->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->postOriginalImagePath.$media->file_name) : asset('backend/assets/images/no-post.png');
                $mediaArr[$x]['file_url'] = $file;
                $mediaArr[$x]['thumb_url'] = !empty($media->thumb_name) ? Storage::disk($this->fileSystemCloud)->url($this->postThumbImagePath.$media->thumb_name) : asset('backend/assets/images/no-post.png');
                $mediaArr[$x]['file_type'] = $media->file_type;
                $x++;
            }
        }
        $row['data']['media'] = $mediaArr;


		return view('business.posts.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : View post likes details.
        @Date   : 04/05/2022
    */

    public function viewPostLikes(Request $request){

		$id     =	$request->object_id;

        $likesRs = PostLike::with('userLikePost')
             ->with('user')
             ->where('post_id',$id)
             ->orderby('created_at','desc')
             ->get();

        $row['data'] = array();
        foreach($likesRs as $userRs){
            $userId =  $userRs->user->id;
            $userFirstName =  $userRs->user->name;
            $userName =  (($userRs->user->role==3) ? $userRs->user->business->company_name : $userRs->user->user_name) ?? '-';
            $role =  $userRs->user->role;
            // if($role == 1){
            //     $userRole = 'Super Admin';
            // }
            // if($role == 2){
            //     $userRole = 'Admin';
            // }
            // if($role == 3){
            //     $userRole = 'Business User';
            // }
            // if($role == 4){
            //     $userRole = 'Employee';
            // }
            // if($role == 5){
            //     $userRole = 'Consumer';
            // }
            $avtar = !empty($userRs->user->avtar) ? Storage::disk(Config::get('constant.FILESYSTEM_CLOUD'))->url(Config::get('constant.USER_ORIGINAL_PHOTO_UPLOAD_PATH').$userRs->user->avtar) : asset('backend/assets/images/no-avtar.png');

            $dateDiff = $userRs->created_at->diffForHumans();

            $row['data'][] = '<div class="align-middle m-b-25"><img src="'.$avtar.'" alt="'.$userFirstName.'" class="img-radius align-top m-r-15"><div class="d-inline-block"> <h6 class="postLike-username">'.$userName.'</h6><span class="status deactive">'.$dateDiff.'</span></div></div>';

        }
		return view('business.posts.viewLikes',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Delete post record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/02/2022
    */

    public function destroy($id){
		$obj = UserPost::find($id);
        // $input['status'] = 0;
        // UserPost::where('id', $id)->update($input);
		$obj->delete();
        Alert::success('Success', 'Post has been deleted successfully!', 'success');
		return redirect('business/posts');
    }

    /*
        @Author : Spec Developer
        @Desc   : Archive post details.
        @Date   : 28/03/2022
    */

    public function archivePosts(Request $request){
        $data['page_title'] = 'Archive Posts';
		$data['page_js'] = array(
            'backend/assets/business/js/posts.js'
        );
        $data['extra_css'] = array(
            'plugins/table/datatable/datatables.css',
            'plugins/table/datatable/datatables.css',
            'plugins/fancybox/dist/jquery.fancybox.min.css',
        );
		$data['extra_js'] = array(
            'plugins/table/datatable/datatables.js',
            'plugins/table/datatable/datatables.js',
            'plugins/fancybox/dist/jquery.fancybox.min.js',
        );

		$data['cdnurl_css'] = array();
		$data['cdnurl_js'] = array();

		$data['init'] = array(
            'Posts.archivePosts();'
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
            $totalRecords = UserPost::with('user:id,user_name,role')
            ->withCount('comments')
            ->withCount('likes')
            ->withCount('flaggedPost')
            ->where('user_id', Auth::user()->id)
            ->onlyTrashed()
            ->select('count(*) as allcount')->count();

            $postObj = UserPost::with('user:id,user_name,role')
            ->withCount('comments')
            ->withCount('likes')
            ->withCount('flaggedPost')
            ->where('user_id', Auth::user()->id)
            ->onlyTrashed()
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

            $totalFilteredRows = $postObj->count();

            $postData = $postObj->skip($start)
                ->take($rowPerPage)
                ->orderBy($columnName, $sortOrder)
                ->get();


            $data_arr = [];
            $data_arr = $postData;
            foreach($postData as $key => $row) {
                $totalWords = str_word_count($row->post_content);
                if($totalWords > 10){
                    $pieces = explode(" ", $row->post_content);
                    $postContent = implode(" ", array_splice($pieces, 0, 10)).' ...';
                } else {
                    $postContent = $row->post_content;
                }
                $data_arr[$key]->post_content = $postContent ?? '-';
                $data_arr[$key]->comments_count = ($row->comments_count > 0) ? '<a href="'.url('business/posts/comments/'.$row->id).'"><span class="badge counter-bg">'.$row->comments_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->comments_count.'</span>';

                $data_arr[$key]->likes_count = ($row->likes_count > 0) ? '<a class="viewLikesRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/view-post-likes').'" title="View"><span class="badge counter-bg">'.$row->likes_count.'</span></a>' : '<span class="badge counter-bg disabled">'.$row->likes_count.'</span>';

                $data_arr[$key]->flagged = ($row->flagged_post_count > 0) ? '<a class="viewFlaggedRecord" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/view-post-flagged').'" title="View flaggd"><span class="badge flag-counter-bg">'.$row->flagged_post_count.'</span></a>' : '<span class="badge flag-counter-bg disabled">'.$row->flagged_post_count.'</span>';

                $data_arr[$key]->post_type = ($row->is_public == 0) ? '<i class="fa fa-user private_icon" title="Private"></i>' : '<i class="fa fa-users public_icon" title="Public"></i>';

                if ($row->user) {
                    $data_arr[$key]->user_id = (($row->user->role==3) ? $row->user->business->company_name : $row->user->user_name) ?? '-';
                }               
                $data_arr[$key]->status = ($row->status == 0) ? '<label class="label label-danger">Deactive</label>' : '<label class="label label-success">Active</label>';

                $btn = '';
                $btn .= '<a class="viewRecord ml-2 mr-2" href="javascript:void(0)" data-id="' . $row->id . '" data-url="'.url('business/posts/archive-view').'" title="View"><i class="fa fa-search-plus fa-action-view"></i></a>';
               $btn .= '<a class="restore ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" title="Restore" data-url="'.url('business/posts/restore').'" data-target="#restoreModal" ><i class="fa fa-refresh fa-action-restore"></i></a>';
               $btn .= '<a class="delete ml-2 mr-2" href="javascript:void(0);" data-id="' . $row->id . '" data-url="'.url('business/posts/force-delete').'" title="Delete" data-target="#DeleteModal"><i class="fa fa-trash fa-action-delete"></i></a>';
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

        return view('business.posts.archive_list',$data);
    }

    /*
        @Author : Spec Developer
        @Desc   : View archive products record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */

    public function viewArchive(Request $request){

		$id     =	$request->object_id;

        $row['data'] = UserPost::with('user:id,user_name,role')
        ->withCount('postMediaRec')
        ->with('postMediaRec:id,post_id,file_name,file_type,thumb_name')
        ->withCount('comments')
        ->withCount('likes')
        ->withCount('flaggedPost')
        ->onlyTrashed()
        ->find($id);

        $mediaArr = array();
        $x = 0;
        if($row['data']->post_media_rec_count > 0){
            foreach($row['data']->postMediaRec as $media){
                $mediaArr[$x]['id'] = $media->id;
                $file = !empty($media->file_name) ? Storage::disk($this->fileSystemCloud)->url($this->postOriginalImagePath.$media->file_name) : asset('backend/assets/images/no-post.png');
                $mediaArr[$x]['file_url'] = $file;
                $mediaArr[$x]['thumb_url'] = !empty($media->thumb_name) ? Storage::disk($this->fileSystemCloud)->url($this->postThumbImagePath.$media->thumb_name) : asset('backend/assets/images/no-post.png');
                $mediaArr[$x]['file_type'] = $media->file_type;
                $x++;
            }
        }
        $row['data']['media'] = $mediaArr;

		return view('business.posts.view',$row);
    }

    /*
        @Author : Spec Developer
        @Desc   : Force delete post record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */

    public function forceDelete($id){
        $originalPath	= Storage::disk($this->fileSystemCloud)->path($this->postOriginalImagePath);
        $thumbPath		= Storage::disk($this->fileSystemCloud)->path($this->postThumbImagePath);
        deletePostMediaFromPostId($id, $originalPath, $thumbPath, 1);
        Alert::success('Success', 'Post has been deleted permanently!', 'success');
        return redirect('business/posts/archive-posts');
    }

    /*
        @Author : Spec Developer
        @Desc   : Restore post record.
        @Output : \Illuminate\Http\Response
        @Date   : 28/03/2022
    */

    public function restorePost($id){

        UserPost::withTrashed()->find($id)->restore();

        $input['status'] = 1;
        UserPost::where('id', $id)->update($input);
        UserPost::withTrashed()->find($id)->restore();

        Alert::success('Success', 'Post has been restore successfully!', 'success');
		return redirect('business/posts/archive-posts');
    }

}