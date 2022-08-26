<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Session;

class Products extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'user_id',
		'product_title',
		'product_slug',
        'product_description',
        'sku',
        'quantity',
        'cost_price',
		'sell_price',
        'status',
    ];

    use Sluggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'product_slug' => [
                'source' => 'product_title'
            ]
        ];
    }

	public function productGalleries(){
        return $this->hasMany(ProductGallery::class, 'product_id');
    }

    //get Users all products
    public static function getAllUsersProducts($userId){
        $products = Products::withCount('galleries')
            ->Where('user_id', $userId)
            ->Where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate();
        return $products;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

	public function productCategory(){
        return $this->hasOne(ProductCategory::class,'id','category_id');
    }

	public function usersData(){
        return $this->hasOne(User::class, 'id','user_id');
    }

	public function usersInfo(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

	public function productMedia(){
        return $this->belongsTo(ProductGallery::class, 'id','product_id');
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