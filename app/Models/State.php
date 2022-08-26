<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $fillable = ['name','country_id','country_code','latitude','longitude'];
    protected $table = 'states';
	
	public function usersData(){
        return $this->hasOne(User::class, 'id','created_by');
    }

    public function getStateCountry() {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
