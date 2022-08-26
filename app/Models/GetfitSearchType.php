<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetfitSearchType extends Model
{
    use HasFactory;
    protected $fillable = ['getfit_id','name','created_by','status'];
    protected $table = 'getfit_search_type';
}
