<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CommentsUpvoteAndDownvote extends Model
{
    use HasFactory;

    protected $fillable = [
        'comments_id',
        'user_id',
        'status',
    ];

      protected $table = 'comments_upvote_and_downvote';

      public static function isLikeCount($id){
        $likeCount = CommentsUpvoteAndDownvote::where('comments_id',$id)->where('status',1)->get();
        if(!empty($likeCount)){
            return count($likeCount);
        }else{
            return 0;
        }
    }


    public static function isUpVote($id){

        $isLike = CommentsUpvoteAndDownvote::where('comments_id',$id)->where('user_id',Auth::User()->id)->where('status',1)->select('id')->first();
        if(!empty($isLike)){
            return true;
        }else{
            return false;
        }

    }

    public static function isDownVote($id){
        $isLike = CommentsUpvoteAndDownvote::where('comments_id',$id)->where('user_id',Auth::User()->id)->where('status',0)->select('id')->first();
        if(!empty($isLike)){
            return true;
        }else{
            return false;
        }

    }

    /**
     * user: Relation with user table for get user's information.
     *
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * commentUpVote: Get all users who has given up vote for comment.
     *
     * @return void
     */
    public function commentUpVote(){
        return $this->hasMany(CommentsUpvoteAndDownvote::class, 'comments_id','id')->where('status',1);
    }

    /**
     * commentDownVote: Get all users who has given down vote for comment.
     *
     * @return void
     */
    public function commentDownVote(){
        return $this->hasMany(CommentsUpvoteAndDownvote::class, 'comments_id','id')->where('status',0);
    }
}