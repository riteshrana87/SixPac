<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFoodData extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','fdiid','protein','carbohydrate','calory','energy','type_of_meal','food_description','date_and_time','food_or_exercise','quantity','serving_qty','serving_size','notes'];
    protected $table = 'user_food_data';
}
