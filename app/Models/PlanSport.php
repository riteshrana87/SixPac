<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class PlanSport extends Model
{
    use HasFactory;

     protected $fillable = [
        'name',
        'created_by',
        'status',
        'icon_file'
    ];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'plan_sports';

    public function user() {
        return $this->hasOne(User::class, 'id','created_by');
    }
    
    /**
     * getCreatedAtAttribute is used to convert UTC date time to Users local date time
     *
     * @param  date   $dateWithTime datetime object
     * @return string return converted date of given format
     * @author Spec Developer
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
