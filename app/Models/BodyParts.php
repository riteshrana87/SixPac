<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyParts extends Model
{
    use HasFactory;
    protected $fillable = ['name','created_by','status','icon_file'];
    protected $table = 'body_parts';

     public function exercises()
    {
        return $this->morphToMany(Exercise::class, 'exercisable');
    }
}
