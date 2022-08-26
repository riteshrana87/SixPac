<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

class PostComment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'post_comments';

    protected $fillable = [
       'post_id',
       'parent_id',
       'user_id',
       'commentable_type',
       'commentable_id',
       'comment',
       'comments_image',
       'comments_video',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }

	public function usersInfo(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

	public function posts(){
        return $this->belongsTo(UserPost::class,'post_id','id');
    }

    public function postWithTrashed(){
        return $this->belongsTo(UserPost::class,'post_id','id')->withTrashed();
    }

    public function commentLike()
    {
        return $this->hasMany(CommentsUpvoteAndDownvote::class, 'comments_id','id')->where('status',1);
    }

    public function commentDownVote()
    {
        return $this->hasMany(CommentsUpvoteAndDownvote::class, 'comments_id','id')->where('status',0);
    }

    public function commentTagTousers(){
        return $this->belongsToMany(User::class, 'comment_tag_to_users', 'comment_id', 'user_id')->withTimestamps();
    }

    /**
     * flaggedComment: Get all flagged comments from comment_id
     *
     * @return void
     */
    public function flaggedComment(){
        return $this->belongsToMany(User::class, 'flag_comments', 'comment_id', 'user_id')->withTimestamps();
    }

    /**
     * commentFlagged: Get all users who has flagged comment.
     *
     * @return void
     */
    public function commentFlagged(){
        return $this->hasMany(FlagComment::class, 'comment_id','id');
    }

    /*
     * Create function for convert UTC date time to Users local date time     *
     *
    */

    public function getCreatedAtAttribute($dateWithTime){
        $dateTime = strtotime($dateWithTime);
        $utc = date("Y-m-d\TH:i:s.000\Z", $dateTime);
        $time = strtotime($utc);

        $getTimeZone = Session::get('user_timezone');
        if($getTimeZone != "UTC" && !empty($getTimeZone)){
            date_default_timezone_set($getTimeZone);
            $strtotime =  date('Y-m-d H:i:s', $time);
        }
        else
        {
            $strtotime = date("Y-m-d H:i:s", $time);
        }
        return date('Y-m-d H:i:s',strtotime($strtotime));
    }

}