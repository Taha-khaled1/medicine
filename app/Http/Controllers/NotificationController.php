<?php

namespace App\Http\Controllers;

use App\Jobs\SendFCMNotificationJob;
use App\Models\User;
use App\Notifications\AdminMessage;
use App\Notifications\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{

    public function sendNotificationToAll(Request $request)
    {
        $messageFromAdmin = $request->message;
        $titleFromAdmin = $request->title;

        if ($request->type == 'all') {
            $users = User::all();
        } else {
            $users = User::where('type', $request->type)->get();
        }

        // or any filtered list of users

        // Sending Notification via Database
        // Notification::send($users, new AdminMessage($messageFromAdmin, $titleFromAdmin));
        Notification::send($users,  new UserMessage($messageFromAdmin, $titleFromAdmin, $messageFromAdmin, $titleFromAdmin, "admin", ""));
        // Dispatch Job for Each User with FCM Token
        foreach ($users as $user) {
            if ($user->fcm) {
                SendFCMNotificationJob::dispatch($user->fcm, $titleFromAdmin, $messageFromAdmin);
            }
        }

        session()->flash('Add', 'تم ارسال الاشعار لجميع المستخدمين بنجاج');
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }


    public function sendNotificationToUser(Request $request)
    {
        $messageFromAdmin = $request->message;
        $titleFromAdmin = $request->title;
        $userId = $request->user_id;

        $user = User::find($userId);
        if (!$user) {
            session()->flash('Error', 'User not found');
            return back();
        }

        // Sending Notification via Database
        // Notification::send([$user], new AdminMessage($messageFromAdmin, $titleFromAdmin));
        Notification::send([$user],  new UserMessage($messageFromAdmin, $titleFromAdmin, $messageFromAdmin, $titleFromAdmin, "admin", ""));
        // Dispatch Job for FCM Notification
        if ($user->fcm) {
            SendFCMNotificationJob::dispatch($user->fcm, $titleFromAdmin, $messageFromAdmin);
            session()->flash('Add', 'تم ارسال الاشعار لهذ المستخدم بنجاج');
        } else {
            session()->flash('Add', 'تم ارسال الاشعار لهذ المستخدم بنجاج ولكن العضو ليس لديه رمز FCM');
        }

        return back();
    }
}
