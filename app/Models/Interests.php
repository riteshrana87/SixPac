<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Interests extends Model
{
    use HasFactory;
    protected $fillable = ['interest_name','icon_file','status'];
    protected $table = 'interests';

    public function subinterests(){
        return $this->hasMany(SubInterests::class, 'interest_id');
    }

    public function getSubInterestName(){
        return $this->hasMany(SubInterests::class,'interest_id','id');
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

    /**
     * Get all of the tags for the post.
     */
    public function exercises()
    {
        return $this->morphToMany(Exercise::class, 'exercisable');
    }

}