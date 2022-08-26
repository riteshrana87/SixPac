<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use App\Models\ConsumerInterests;
use App\Models\SubInterests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpParser\JsonDecoder;

class ConsumerInterestsController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : update the specified resource in storage..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 31/01/2022
    */
    public function update(Request $request)
    {
        //dd($request->all());
        try {
            $user_id = Auth::user()->id;
            $validator = Validator::make($request->all(), [
                'interest' => 'required',
            ]);

            if ($validator->fails()) {
                Log::info('Store consumer interest details :: message :' . $validator->errors());
                return response()->json(['message' => $validator->errors(), 'status' => 422], 422);
            }

            ConsumerInterests::where('user_id',$user_id)->delete();

            $interests = json_decode($request->interest);
            foreach($interests as $interest){
                $consumer['user_id'] = $user_id;
                $consumer['interest_id'] = $interest->interestId;
                if(isset($interest->subInterestId) && !empty($interest->subInterestId)){
                    $subInterests = $interest->subInterestId;
                    foreach($subInterests as $subInterest){
                        $consumer['sub_interest_id'] = $subInterest;
                        ConsumerInterests::create($consumer);
                    }
                }

                if(isset($interest->subInterestText) && !empty($interest->subInterestText)){
                    $subInterestsData = $interest->subInterestText;
                    foreach($subInterestsData as $value){
                        $inputData['interest_id'] = $interest->interestId;
                        $inputData['sub_interest_name'] = $value;
                        $inputData['other'] = 1;
                        $inputData['created_by'] = $user_id;
                        $inputData['updated_by'] = $user_id;
                        $subData = SubInterests::create($inputData);

                        $newData['user_id'] = $user_id;
                        $newData['interest_id'] = $interest->interestId;
                        $newData['sub_interest_id'] = $subData->id;
                        ConsumerInterests::create($newData);
                    }
                }
            }
            
            Log::info('Consumer interest details Update Successfully :: message :Consumer interest Update.');
            return successResponseWithoutData(trans('api-message.INTEREST_UPDATED_SUCCESSFULLY'), STATUS_CODE_SUCCESS);
        } catch (\Exception $e) {
            // Log error message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return errorResponse(trans('api-message.DEFAULT_ERROR_MESSAGE'),STATUS_CODE_ERROR);
        }
    }

     /*
       @Author : Ritesh Rana
       @Desc   : Remove the specified resource from storage.
       @Input  : int  $id
       @Output : \Illuminate\Http\Response
       @Date   : 31/01/2022
   */
  public function destroy($id)
  {
      try {
          Log::info('Delete Consumer Interests API started !!');
          $consumer = ConsumerInterests::find($id);
          if (empty($consumer)) {
              Log::warning('Consumer Interests not found with id:' . $id);
              return response()->json(['message' => 'Consumer Interests not found','status' => 400], 400);
          }
          $consumer->delete();
          Log::info('Delete Consumer Interests with id:' . $id);
          return response()->json(['message' => "Consumer Interests deleted !!",'status' => 200], 200);
      } catch (\Exception $e) {
          Log::error('Unable to delete Consumer Interests due to err: ' . $e->getMessage());
          return response()->json(['message' => "Unable to delete Consumer Interests !!",'status' => 500], 500);
      }
  }
}
