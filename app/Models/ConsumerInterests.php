<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interests;
use App\Models\SubInterests;
use Illuminate\Support\Facades\Session;

class ConsumerInterests extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','interest_id','sub_interest_id'];
    protected $table = 'consumer_interests';


    public function getInterestName(){
        return $this->hasOne(Interests::class,'id','interest_id','user_id')->with('getSubInterestName');

    }


    public function getSubInterestName(){
        return $this->hasMany(SubInterests::class,'id','sub_interest_id');
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