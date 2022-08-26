<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{

     /**
     * send is used to send push notifictaion to ios device
     *
     * @param  string $token        device token
     * @param  array  $notification notification array
     * @return mixed  success if sent otherwise false
     * @author Spec Developer
     */
    public static function send($deviceToken, $notification)
    {
        try {
            Log::info('Start Send Notification');
            if (empty($deviceToken) || empty($notification) || !config('constant.FCM_API_KEY_DATA')) {
                Log::info('deviceToken or notification is empty');
                return false;
            }
            $notificationData['registration_ids'] = $deviceToken;
            $notificationData['data'] = [
                    'alert' => $notification['message'],
                    'message' => $notification['message'],
                    'sound' => 'default',
                    'postId' => $notification['postId']
                ];
            $notificationData['notification'] = ['body' => $notification['message']];

            Log::info('Before send notification');
            $isSent = Http::acceptJson()->withToken(config('constant.FCM_API_KEY_DATA'))
            ->post(
                'https://fcm.googleapis.com/fcm/send',
                $notificationData
            );
           $response = json_decode($isSent, 1);
           if (!empty($response)) {
                if ($response['success'] == 0) {
                    $errMsg = $response['results'][0]['error'];
                    Log::error('Errors=> '.print_r($errMsg,1));
                } else {
                    Log::info('Notification sent successfully.');
                }
            }
            Log::info($response);
            Log::info('End Send Notification');
        } catch (\Exception $e) {
            Log::info('Erros => '.print_r($e->getMessage(), 1));
        }
    }
}
