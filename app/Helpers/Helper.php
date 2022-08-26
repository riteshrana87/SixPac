<?php

namespace App\Helpers;

use App\Models\Notification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Storage;

class Helper
{


    /**
     * This method is  used for user Notification.
     * @Created On: 10-02-2022;
     * @Update On : 10-02-2022;
     * @Updated By: SPEC INDIA;
     * @Author: SPEC INDIA
     * @version: 1.0.0
    */

    public static function userNotification($data,$id = null){
        try {
                $notification = new Notification();
                $notification->sender_id = $data['sender_id'];
                $notification->receiver_id = $data['receiver_id'];
                $notification->notification_type = $data['type'];
                $notification->post_id = $data['post_id'];
                $notification->status = $data['status'];
                //$notification->post_comments_id = $id;
                $notification->save();
        
            Log::info('Notification add successfully ' . $notification->id);

            return $notification;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Unable to notification due to err: ' . $e->getMessage());
             }
    }

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
                'result' => 'error',
                'stausCode' => $statuscode,
                'message' => $errorMsg,
                'error' => $error
            ], $statuscode);
        }
        return response()->json([
            'result' => 'error',
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
            'result' => 'success',
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
            'result' => 'success',
            'stausCode' => $statuscode,
            'message' => $message,
        ], $statuscode);
    }

   
    static function sendPushNotification($notificationData) {
        $fcm_api_key = Config::get('constant.FCM_API_KEY_DATA');
        //$fcm_api_key = 'AAAABLfJ05I:APA91bE85mbtMC0Hfcsehib2AuWzycHN0cL9weQvyB3TqW3ueWNvlxeikYRUXXkQRgBd1QgQ7pW04efAm3aOqanD69iHvtFuI030AFlM9LVoBzAOjYtteZJsR-IMlNUKMxuW1rokjh9Q';
        
        //dd($fcm_api_key);
        $headers = array(
            'Authorization: key=' . $fcm_api_key,
            'Content-Type: application/json'
        );
        

        $msg = array(
            'alert' => $notificationData['message'],
            //'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'sound' => 'default',
            'postId' => $notificationData['postId'],
            //'icon' => $notificationData['icon'],
        );
        
        $notification = array(
            //'title' => $notificationData['title'],
            'body' => $notificationData['message']
        );
        $fields = array(
            'registration_ids' => $notificationData['deviceToken'],
            'data' => $msg,
            'priority' => 'high',
            'notification' => $notification
        );

        // $fields = array(
        //     'to' => 'eZeHi8JFbUOMvLI0RD37H1:APA91bFVwA0omDZ9nymV4uAlNtuUXH9OC7dXAbE18eoVUjMeGhqO_GcVmByhBEHEV7Neg5SLlVGzmIX44T96n09_LJ-8rngH0f5xyduiGYukT_Y4DpNSv1acZua_N-zxiBq5iomtdBsp',
        //     'data' => $msg,
        //     'priority' => 'high',
        //     'notification' => $notification
        // );

        try {
            Log::info('Start Send Notification');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
            $res = (array) json_decode($result);
            $errMsg = '';
            if (!empty($res)) {
                if ($res['failure'] == 1) {
                    $errMsg = $res['results'][0]->error;
                    Log::error($errMsg);
                }
            }
            Log::info('End Send Notification');
            Log::info($res);
            Log::info(json_encode($notification));

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }


    static function sendPushNotificationNew($notificationData) {
        $fcm_api_key = Config::get('constant.FCM_API_KEY_DATA');
        //$fcm_api_key = 'AAAABLfJ05I:APA91bE85mbtMC0Hfcsehib2AuWzycHN0cL9weQvyB3TqW3ueWNvlxeikYRUXXkQRgBd1QgQ7pW04efAm3aOqanD69iHvtFuI030AFlM9LVoBzAOjYtteZJsR-IMlNUKMxuW1rokjh9Q';
        
        //dd($fcm_api_key);
        $headers = array(
            'Authorization: key=' . $fcm_api_key,
            'Content-Type: application/json'
        );
        

        $msg = array(
            'alert' => $notificationData['message'],
            //'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'sound' => 'default',
            'postId' => $notificationData['postId'],
            //'icon' => $notificationData['icon'],
        );
        
        $notification = array(
            //'title' => $notificationData['title'],
            'body' => $notificationData['message']
        );
        $fields = array(
            'to' => $notificationData['deviceToken'],
            'data' => $msg,
            'priority' => 'high',
            'notification' => $notification
        );
        $apiURL = 'https://fcm.googleapis.com/fcm/send';

        try {
            Log::info('Start Send Notification');
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', $apiURL, ['form_params' => $fields, 'headers' => $headers]);
            dd($response);
            $responseBody = json_decode($response->getBody(), true);
            dd($responseBody);
            echo $statusCode = $response->getStatusCode(); // status code
        
            dd($responseBody);

            Log::info('End Send Notification');
           // Log::info($res);
            Log::info(json_encode($notification));

        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    function objectToArray(&$object)
    {
        return @json_decode(json_encode($object), true);
    }

     
}
