<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead($notificationId)
    {
        $user = Auth::user(); // Get the authenticated user

        $notification = $user->notifications()->find($notificationId); // Find the specific notification
        if ($notification) {
            $notification->markAsRead(); // Mark the notification as read
        }

        return redirect()->back(); // Redirect back to the previous page
    }

    public function index()
    {
        $user = Auth::user(); // Get the authenticated user
        $notifications = $user->notifications; // Retrieve all notifications
        return view('notifications.index', compact('notifications')); // Pass notifications to the view
    }
}
