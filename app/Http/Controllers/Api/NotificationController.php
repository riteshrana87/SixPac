<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsernotificationResource;
use App\Models\Notification;
use App\Services\LengthPager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /*
        @Author : Ritesh Rana
        @Desc   : Get list of all Notification ..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 20/06/2022
    */
    public function getNotificationUser(Request $request)
    {
        try {

            If (!empty($request->page)) {
                $page = $request->page;
            } else {
                $page = "1";
            }
            If (!empty($request->perpage)) {
                $perpage = $request->perpage;
            } else {
                $perpage = Config::get('constant.LIST_PER_PAGE');
            }
            $offset = ($page - 1) * $perpage;
            $user_id = Auth::user()->id;
            
            $unread_count = Notification::Where('receiver_id', $user_id)
                ->where('status', 0)
                ->orderBy('created_at', 'DESC')
                ->get();
                $unreadRows = count($unread_count);
            
            $notification_count = Notification::Where('receiver_id', $user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
            $numrows = count($notification_count);

            $notification = Notification::Where('receiver_id', $user_id)
                ->orderBy('created_at', 'DESC')
                ->skip($offset)
                ->take($perpage)
                ->get();
                
            $notificationCollection = UsernotificationResource::collection($notification);

            $nextPage = LengthPager::makeLengthAware($notificationCollection, $numrows, $perpage, []);
            Log::info('User Notification  List');

            // Get response success
            $data = array(
                'totalCount' => $numrows,
                'perpage' => $perpage,
                'nextPage' => $nextPage,
                'unreadCount' => $unreadRows,
                'notificationList' => $notificationCollection
            );
            return successResponse(trans('api-message.NOTIFICATION_LIST_FETCHED_SUCCESSFULLY'), STATUS_CODE_SUCCESS, $data);

        } catch (\Exception $e) {
            // Log Message
            Log::error(strtr(trans('log-messages.DEFAULT_ERROR_MESSAGE'), [
                '<Message>' => $e->getMessage(),
            ]));
            return response()->json([
                'status' => 500,
                'message' => trans('api-message.DEFAULT_ERROR_MESSAGE'),
            ]);
        }
    }

/*
        @Author : Ritesh Rana
        @Desc   : Read the specified notification..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function read(Request $request)
    {
        try {
            $notificationId = $request->get('id');

            $notificationData = Notification::find($notificationId);
            $notificationData->status = 1;
            $notificationData->update();
            
            return successResponseWithoutData(trans('api-message.NOTIFICATION_READ_STATUS_UPDATED'), STATUS_CODE_SUCCESS);
            
        } catch(\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }


/*
        @Author : Ritesh Rana
        @Desc   : Read all the notification..
        @Input  : \Illuminate\Http\Request $request
        @Output : \Illuminate\Http\Response
        @Date   : 28/01/2022
    */
    public function readAll(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            Notification::where('receiver_id', $user_id)
                ->where('status',0)
                ->update([
                    'status' => 1
                ]);
            return successResponseWithoutData(trans('api-message.NOTIFICATION_READ_STATUS_UPDATED'), STATUS_CODE_SUCCESS);
        } catch(\Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
}
