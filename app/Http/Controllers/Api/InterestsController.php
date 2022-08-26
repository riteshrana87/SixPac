<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterestsResource;
use App\Models\Interests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterestsController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : Show the form for editing the specified resource.
        @Input  : int  $id
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function getInterestsData()
    {
        try{
            $interest = Interests::with(array('subinterests'=> function($query) {
                        $query->select('id','interest_id','sub_interest_name');
                }))->where(array('status' => 1))
                ->get();

            $interests = InterestsResource::collection($interest);
            /*get users details*/
            $data = array(
                'interests' => $interests,
            );
            return successResponse(trans('api-message.INTERESTS_AND_SUB_INTERESTS'), STATUS_CODE_SUCCESS, $data);
        } catch (\Exception $e) {
            // Log social login error messages
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }
}
