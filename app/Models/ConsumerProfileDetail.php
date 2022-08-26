<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class ConsumerProfileDetail extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','preferred_pronoun','height','weight','location_status','address','unit_apt','city','state','country','latitude','longitude','activity_level','fitness_status','zipcode','daily_calories','target_weight','weight_gain_loss_frequency','weight_goal','activity_frequency','update_data','goal_completion_date','measurement_type','burned_calory','starting_weight'];
    protected $table = 'consumer_profile_detail';

    public function user()
    {
        return $this->belongsTo(User::class);
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