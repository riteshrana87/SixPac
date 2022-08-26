<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Session;

class UserPost extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'squad_id',
        'post_title',
        'post_slug',
        'post_content',
        'notes',
        'status',
        'share_status',
        'is_public',
        'created_by',
    ];

   // use Sluggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_posts';

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    // public function sluggable()
    // {
    //     return [
    //         'post_slug' => [
    //             'source' => 'post_title'
    //         ]
    //     ];
    // }

    public function galleries(){
        return $this->hasMany(PostGallery::class, 'post_id')->orderBy('media_order');
    }


    public function postTagTousers(){
        //return $this->hasMany(PostTagTousers::class, 'post_id');
        return $this->belongsToMany(User::class, 'post_tag_to_users', 'post_id', 'user_id')->withTimestamps();
    }

    public function likes(){
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->withTimestamps();
    }

    public function comments(){
        return $this->morphMany(PostComment::class, 'commentable')->withCount('commentLike')->withCount('commentDownVote')->whereNull('parent_id')->orderBy('post_comments.id', 'DESC');
    }

    public function most_like_comments(){
        return $this->morphMany(PostComment::class, 'commentable')->withCount('commentLike')->orderBy('comment_like_count','DESC')->limit(2)->whereNull('parent_id')->orderBy('post_comments.id', 'DESC');
    }

    //get Users all posts
    public static function getAllUsersPost($userId){
        $posts = UserPost::withCount('galleries')
            ->Where('user_id', $userId)
            ->Where('status', 1)
            ->Where('is_public', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate();
        return $posts;
    }

    public function postCreater(){
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

	public function usersData(){
        return $this->hasOne(User::class, 'id','user_id');
    }

	public function usersInfo(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

	public function postMedia(){
        return $this->belongsTo(PostGallery::class, 'id','post_id');
    }

    public function postMediaRec(){
        return $this->hasMany(PostGallery::class, 'post_id','id')->orderBy('media_order');
    }

    /**
     * flaggedPost: Get all flagged post from post_id
     *
     * @return void
     */
    public function flaggedPost(){
        return $this->belongsToMany(User::class, 'flag_comments', 'post_id', 'user_id')
        ->where(function ($query) {
            $query->where('comment_id', '=', '')
            ->orWhereNull('comment_id');
        })
        ->withTimestamps();
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