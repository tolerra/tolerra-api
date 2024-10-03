<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getNotifications($user_id)
    {
        $notifications = Notification::where('user_id', $user_id)->get();
        return response()->json($notifications);
    }

    public function updateNotification(Request $request, $user_id, $notification_id)
    {
        $notification = Notification::where('user_id', $user_id)->findOrFail($notification_id);

        $notification->update(array_merge($request->all(), ['read' => true]));

        return response()->json($notification);
    }
}