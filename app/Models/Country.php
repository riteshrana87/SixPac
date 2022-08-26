<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = ['name','region','phonecode','subregion','capital','currency','currency_symbol','timezones','latitude','longitude'];
    protected $table = 'countries';
	
	/* public function usersData(){
        return $this->hasOne(User::class, 'id','created_by');
    } */

    
}
