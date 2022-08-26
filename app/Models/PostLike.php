<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PostLike extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_id',
        'user_id',
    ];

      protected $table = 'post_likes';

      public function userLikePost()
      {
          return $this->hasMany(UserPost::class,'id','post_id');
      }

      public function user()
      {
          return $this->belongsTo(User::class,'user_id','id');
      }

      public static function isLike($id){

      $isLike = PostLike::where('post_id',$id)->where('user_id',Auth::User()->id)->first();

      if(!empty($isLike)){
          return true;
      }else{
          return false;
      }

      }

      /*
     * Create function for convert UTC date time to Users local date time     *
     *
    */

    // public function getCreatedAtAttribute($dateWithTime){
    //     $dateTime = strtotime($dateWithTime);
    //     $utc = date("Y-m-d\TH:i:s.000\Z", $dateTime);
    //     $time = strtotime($utc);

    //     $getTimeZone = Session::get('user_timezone');
    //     if($getTimeZone != "UTC" && !empty($getTimeZone)){
    //         date_default_timezone_set($getTimeZone);
    //         $strtotime =  date('Y-m-d H:i:s', $time);
    //     }
    //     else
    //     {
    //         $strtotime = date("Y-m-d H:i:s", $time);
    //     }
    //     return date('Y-m-d H:i:s',strtotime($strtotime));
    // }
}