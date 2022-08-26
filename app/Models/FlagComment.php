<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FlagComment extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','post_id','comment_id'];
    protected $table = 'flag_comments';

    /**
     * user: Relation with user table for get user's information.
     *
     * @return void
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * flagBy: Comment wise get all users who has flagged for comment.
     *
     * @return void
     */

    public function flagBy(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public static function postFlag($id){

        $isFlag = FlagComment::where('post_id',$id)->where('user_id',Auth::User()->id)->select('id')->first();
        if(!empty($isFlag)){
            return true;
        }else{
            return false;
        }

    }

    public static function commentFlag($commentId){

        $isFlag = FlagComment::where('comment_id',$commentId)->where('user_id',Auth::User()->id)->select('id')->first();
        if(!empty($isFlag)){
            return true;
        }else{
            return false;
        }

    }

    

}