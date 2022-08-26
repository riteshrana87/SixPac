<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
     * This method is  used for pass error response.
     * @Created On: 10-02-2022;
     * @Update On : 10-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    function errorResponse($errorMsg, $statuscode, $error=null)
    {
        if($error != null) {
            return response()->json([
                'result' => 0,
                'stausCode' => $statuscode,
                'message' => $errorMsg,
                'error' => $error
            ], $statuscode);
        }
        return response()->json([
            'result' => 0,
            'stausCode' => $statuscode,
            'message' => $errorMsg
        ], $statuscode);
    }


    /**
     * This method is  used for pass success response.
     * @Created On: 10-02-2022;
     * @Update On : 10-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    function successResponse($message, $statuscode, $data = null)
    {
        return response()->json([
            'result' => 1,
            'stausCode' => $statuscode,
            'message' => $message,
            'data' => $data
        ], $statuscode);
    }


    /**
     * This method is  used for pass success response without data.
     * @Created On: 10-02-2022;
     * @Update On : 10-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    function successResponseWithoutData($message,$statuscode)
    {
        return response()->json([
            'result' => 1,
            'stausCode' => $statuscode,
            'message' => $message,
        ], $statuscode);
    }

     /**
     * This method is  used for set token expiry time.
     * @Created On: 18-02-2022;
     * @Update On : 18-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    function setTokenExpiryTime(){
        $setTokenExpTime = Carbon\Carbon::now()->addMilliseconds(PERSONAL_ACCESS_TOKEN_EXPIRY_TIME);  // addSeconds // addDays // addWeeks // addMonths
        return $setTokenExpTime;
    }

     /**
     * This method is  used for set token refresh time.
     * @Created On: 18-02-2022;
     * @Update On : 18-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    function setTokenRefreshTime(){
        $setTokenExpTime = Carbon\Carbon::now()->addMilliseconds(PERSONAL_ACCESS_TOKEN_REFRESH_TIME);  // addSeconds // addDays // addWeeks // addMonths
        return $setTokenExpTime;
    }

    /**
     * This method is  used for append log with send email.
     * @Created On: 18-02-2022;
     * @Update On : 18-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    function appendLogWithEmail($page, $message) {
        Log::info($message.PHP_EOL."------------------------------------------------------------");
        //Sender
        $subject = "Error:: ".FRONT_URL." ".$page;
        $name = SUPER_ADMIN_NAME;
        $emails = unserialize (ERROR_EMAILS_SEND_TO);
        $data = array(
            "subject"=> $subject,
            "name" => SUPER_ADMIN_NAME,
            "msg" => $message,
            "companyname" => APP_NAME
        );

        /* Send Mail  End */
        Mail::send('mails.log_messge', $data, function ($message) use ($emails, $subject, $name) {
            for($i=0; $i<count($emails); $i++) {
                $message->to($emails[$i], $name);
            }
            $message->subject($subject);
        });
    }
?>