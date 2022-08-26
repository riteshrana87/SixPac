<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserFollower extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'follower_id',
        'status'
    ];


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_followers';

    public static function followeUserGet($id){
        $follower = UserFollower::where('user_id',Auth::user()->id)
            ->where('follower_id',$id)
           // ->select('status')
            ->first();

        if(!empty($follower)){
            return $follower->status;
        }else{
            return 0;
        }

    }

    public static function followingUserGet($id){

        $follower = UserFollower::where('user_id',$id)
            ->where('follower_id',Auth::user()->id)
            ->select('status')
            ->first();

        if(!empty($follower)){
            return $follower->status;
        }else{
            return 0;
        }

    }


    public static function followingGetId($id){
            
            $follower = UserFollower::where('user_id',Auth::user()->id)
            ->where('follower_id',$id)
            ->where('status',2)
            ->select('id')
            ->first();
        if(!empty($follower)){
            return $follower->id;
        }else{
            return 0;
        }

    }


    public static function followersGetId($id){
        $follower = UserFollower::where('user_id',$id)
        ->where('follower_id',Auth::user()->id)
        ->where('status',2)
        ->select('id')
        ->first();

    if(!empty($follower)){
        return $follower->id;
    }else{
        return 0;
    }

}

public static function checkUserfollowing($id){
    $following = UserFollower::where('user_id',Auth::user()->id)
        ->where('follower_id',$id)
        ->where('status','2')
        ->first();

    if(!empty($following)){
        return 1;
    }else{
        return 0;
    }

}

public static function checkUserfollower($id){
    $follower = UserFollower::where('user_id',$id)
        ->where('follower_id',Auth::user()->id)
        ->where('status','2')
        ->first();

    if(!empty($follower)){
        return 1;
    }else{
        return 0;
    }

}
}
