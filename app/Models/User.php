<?php

namespace App\Models;

use App\Http\Middleware\Consumer;
use App\Http\Resources\InterestsResource;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
//use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'name',
        'user_name',
        'email',
        'phone',
        'quick_blox_id',
        'facebook_id',
        'google_id',
        'apple_id',
        'date_of_birth',
        'password',
        'avtar',
        'app_version',
        'status',
        'social_flag',
        'gender',
        'gender_pronoun',
        'referral_code',
        'is_email_verified',
        'is_mobile_verified',
        'otp_verified',
        'created_by',
        'updated_by',
        'update_data',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function otpVerified()
    {
        return $this->belongsTo(OtpVerified::class, 'user_id')->withTimestamps();
    }

    public function consumer()
    {
        return $this->hasOne(ConsumerProfileDetail::class, 'user_id');
        //return $this->belongsTo(ConsumerProfileDetail::class, 'user_id');
    }

    public function adminData()
    {
        return $this->hasOne(User::class,'id','id')->where('role', 2);
        //return $this->belongsTo(ConsumerProfileDetail::class, 'user_id');
    }

    public function consumerData()
    {
        return $this->hasOne(User::class,'id','id')->where('role', 5);
        //return $this->belongsTo(ConsumerProfileDetail::class, 'user_id');
    }

    public function businessData()
    {
        return $this->hasOne(User::class,'id','id')->where('role', 3);
        //return $this->belongsTo(ConsumerProfileDetail::class, 'user_id');
    }

	public function business()
    {
        return $this->hasOne(BusinessProfileDetail::class, 'user_id');
        //return $this->belongsTo(BusinessProfileDetail::class, 'user_id');
    }

    public function UserInterests()
    {
        return $this->hasMany(ConsumerInterests::class,'user_id');
        //return $this->belongsToMany(User::class,'consumer_interests','user_id')
        //->withTimestamps();
    }

    public static function interests($userId)
    {
        $getsubins = ConsumerInterests::Where('user_id', $userId)->get()->pluck('sub_interest_id')->toArray();

        $user = User::with(array('consumer'=> function($query) {
            $query->select('user_id','preferred_pronoun','height','weight','location_status','city','state','country','latitude','longitude','activity_level','fitness_status','zipcode','daily_calories','target_weight','weight_gain_loss_frequency','weight_goal','activity_frequency');
        }, 'UserInterests'=> function($query) {
            // $query->select('user_id','interest_id','sub_interest_id')->distinct('interest_id');
			$query->select('user_id','interest_id','sub_interest_id')->groupBy('interest_id');
        }, 'UserInterests.getInterestName'=> function($query) {
            $query->select('id','interest_name');
        }, 'UserInterests.getInterestName.subinterests'=> function($query) {
            $query->select('id','interest_id','sub_interest_name');
        }))
        ->where('id', $userId)
        ->get();

        //dd($user);

        $interest = array();
        //$subInterest['subInterest'] = array();
        $interestsData = $user[0]['UserInterests'];
        foreach($interestsData as $interestData){
            $ins = $interestData->getInterestName;
            $tempMain = array();
            $tempMain['interestId'] = $ins->id;
            $tempMain['interestName'] = $ins->interest_name;

            $subIns = $ins->subinterests;

            foreach($subIns as $data){

                if (in_array($data->id, $getsubins))
                    {
                        $temp_sub_array = array();
                        $temp_sub_array['subInterestId'] = $data->id;
                        $temp_sub_array['subInterestName'] = $data->sub_interest_name;
                        $tempMain['subInterest'][] = $temp_sub_array;
                    }
            }

            $interest[] = $tempMain;
        }

        return $interest;
    }

    public function getConsumerInterests(){
        return $this->hasMany(ConsumerInterests::class,'user_id','id');
    }

    /**
     * get the following
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class,'user_followers','user_id','follower_id')->where('user_followers.status','=',2)->withTimestamps();
    }

     /**
     * get the followers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class,'user_followers','follower_id','user_id')->where('user_followers.status','=',2)->withTimestamps();
    }

    public function post()
    {
        return $this->hasMany('App\Models\UserPost','user_id');
    }

    /**
     * get the following1
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following1()
    {
        return $this->belongsToMany(User::class,'user_followers','user_id','follower_id')
           // ->where('user_followers.status','=',1)
            ->withTimestamps();
    }

    public static function userFollowing(){
        //$following = auth()->user()->following1()->pluck('follower_id')->toArray();
        $following = auth()->user()->following()->pluck('follower_id')->toArray();
        $userIds = array_unique($following);
        return $userIds;
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
     * check send notification following users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function checkSendNotificationToAddPost()
    {
        return $this->belongsToMany(User::class,'user_followers','user_id','follower_id')->where('user_followers.status',2)->withTimestamps();
    }


}