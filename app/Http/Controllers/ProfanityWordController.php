<?php

namespace App\Http\Controllers;

use App\Models\ProfanityWord;
use Illuminate\Http\Request;

class ProfanityWordController extends Controller
{
    public function getProfanityWords(Request $request){
        $profanityWordRs =ProfanityWord::select('word')->where('status',1)->get();

        $newArr = array();
        if(count($profanityWordRs) > 0){
            $x = 0;
            foreach($profanityWordRs as $words){
                $newArr[$x] = $words['word'];
                $x++;
            }
        }
        return $newArr;
    }
}