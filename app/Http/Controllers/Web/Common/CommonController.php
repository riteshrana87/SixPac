<?php

namespace App\Http\Controllers\Web\Common;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\HashTags;

class CommonController extends Controller
{
    public function getHashTags(){
		
		$hashTagRs = HashTags::select('hash_tag_name')->where('status',1)->orderBy('hash_tag_name','asc')->get();
		$hashTagNames = array();
		
		if(count((array)$hashTagRs) > 0){
			foreach($hashTagRs as $hasTags){
				$hashTagNames[] = $hasTags->hash_tag_name;
			}
		}		
		return json_encode($hashTagNames);
	}
	
	public function getUsers(){
		
		$userRs = User::select('user_name')->where('status', 1)->whereNotNull('user_name')->whereNotIn('role', [1, 2])->orderBy('user_name','asc')->get();		
		$arrayRs = array();
		
		if(count((array)$userRs) > 0){
			foreach($userRs as $users){
				$arrayRs[] = $users->user_name;
			}
		}		
		return json_encode($arrayRs);
	}
}
