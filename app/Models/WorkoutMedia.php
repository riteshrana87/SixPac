<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutMedia extends Model
{
    use HasFactory;
    protected $fillable = ['file_name','file_type','thumb_name','short_video','is_banner','workout_mediable_id','workout_mediable_type'];
    protected $table = 'workout_media';
}
