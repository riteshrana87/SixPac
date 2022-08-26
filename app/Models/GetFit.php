<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GetFit extends Model
{
    use HasFactory;
    protected $fillable = ['name','created_by','status'];
    protected $table = 'getfit';
}
