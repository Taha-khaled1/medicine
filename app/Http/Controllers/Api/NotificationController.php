<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendFCMNotificationJob;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class NotificationController extends Controller
{
    public function getUserNotifications(Request $request)
    {
        $countNotifications = $request->user->unreadnotifications->count();
        $notifications = $request->user->notifications;
        return response()->json([
            'status_code' => 200,
            'message' => "Success",
            'notifications' => $notifications,
            'countUnreadNotifications' => $countNotifications,
        ], 200);
    }


    public function getUnReadNotifications(Request $request)
    {
        $countNotifications = $request->user->unreadnotifications->count();
        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
            'countNotifications' => $countNotifications,

        ], 200);
    }




    public function markAllAsRead(Request $request)
    {
        $request->user->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read',
            'status_code' => 200,
        ], 200);
    }

    public function markAsRead($notificationId, Request $request)
    {
        $notification = $request->user->notifications->where('id', $notificationId)->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found',
                'status_code' => 404,

            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',

        ], 200);
    }
    public function deleteNotification(Request $request, $notificationId)
    {
        $user = $request->user;

        $notification = $user->notifications()->where('id', $notificationId)->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found',
                'status_code' => 404,
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
            'status_code' => 200,
        ], 200);
    }

    public function deleteAllNotifications(Request $request)
    {
        $request->user->notifications()->delete();

        return response()->json([
            'message' => 'All notifications deleted successfully',
            'status_code' => 200,
        ], 200);
    }
    public function sendNotficationTest(Request $request)
    {
        try {
            $user = $request->user;
            FacadesNotification::send($user,  new UserMessage("يجب الدفع الباقه لتستطيع امتلاك المميزات الخاصه بك سعر الباقه 365 ريال", "عنوان الاشعار", "messageFromAdmin", "titleFromAdmin", "admin", "1"));
            // foreach ($users as $user) {
            if ($user->fcm) {
                SendFCMNotificationJob::dispatch(
                    $user->fcm,
                    "دفع الباقه الخاصه بك",
                    "يجب الدفع الباقه لتستطيع امتلاك المميزات الخاصه بك سعر الباقه 365 ريال",
                    [
                        "title" => "عنوان الاشعار",
                        "message" => "تم ارسال الاشعار بنجاج",
                        "title_en" => "عنوان الاشعار",
                        "message_en" => "تم ارسال الاشعار بنجاج",
                        "key" => "admin",
                        "keyId" => "1",
                    ]
                );
                // }
            }
            return response()->json([
                'message' => 'All notifications send successfully',
                'status_code' => 200,
            ], 200);
        } catch (\Throwable $th) {
            return errorResponse($th->getMessage(), 500);
        }
    }
}
