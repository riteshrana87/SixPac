<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foods extends Model
{
    use HasFactory;
    protected $fillable = ['fdc_id','description','brand_name','upin_gstin','serving_size','serving_qty','energy','protein','carbohydrate','food_or_exercise'];
    protected $table = 'food_master';
}
