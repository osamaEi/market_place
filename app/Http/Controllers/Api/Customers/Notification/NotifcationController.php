<?php

namespace App\Http\Controllers\Api\Customers\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotifcationController extends Controller
{ 
    public function getNotifications() 
    {
        try { 
            $notifications = \App\Models\Notification::where('customer_id', auth()->guard('customer')->id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'notifications' => $notifications,
                    'unread_count' => $this->getUnreadCount()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch notifications'.$e
            ], 500);
        }
    }

    // Mark single notification as read
    public function markAsRead($id)
    {
        try {
            $notification = \App\Models\Notification::where('customer_id', auth()->guard('customer')->id())
                ->where('id', $id)
                ->first();

            if (!$notification) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Notification not found'
                ], 404);
            }

            $notification->update([
                'is_read' => true,
                'read_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification marked as read',
                'unread_count' => $this->getUnreadCount()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to mark notification as read')
            ], 500);
        }
    }

    // Mark all notifications as read
    public function markAllAsRead()
    {
        try {
            \App\Models\Notification::where('customer_id', auth()->guard('customer')->id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json([
                'status' => 'success',
                'message' => __('All notifications marked as read'),
                'unread_count' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to mark notifications as read')
            ], 500);
        }
    }

    // Get unread notifications only
    public function getUnreadNotifications()
    {
        try {
            $notifications = \App\Models\Notification::where('customer_id', auth()->guard('customer')->id())
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'notifications' => $notifications,
                    'unread_count' => $this->getUnreadCount()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch unread notifications'
            ], 500);
        }
    }

  

    public function close_notification(Request $request)
    {
        $customer = auth()->guard('customer')->user(); // Get the authenticated customer

        if ($customer) {
            $customer->fcm_token = null; // Delete the FCM token
            $customer->save();

            return response()->json(['message' => __('FCM Token deleted successfully.')]);
        }

        return response()->json(['message' => 'Customer not found.'], 404);
    }

    // Open notification - Create new FCM token
    public function open_notification(Request $request)
    {
        $customer = auth()->guard('customer')->user(); // Get the authenticated customer

        if ($customer) {
            $request->validate([
                'fcm_token' => 'required|string',
            ]); // Validate the incoming FCM token

            $newFcmToken = $request->fcm_token; // Get the new FCM token from the request
            $customer->fcm_token = $newFcmToken; // Store the new FCM token
            $customer->save();

            return response()->json(['message' => 'FCM Token created successfully.', 'fcm_token' => $newFcmToken]);
        }

        return response()->json(['message' => 'Customer not found.'], 404);
    }
    


    // Helper function to get unread count
    private function getUnreadCount()
    {
        return \App\Models\Notification::where('customer_id', auth()->guard('customer')->id())
            ->where('is_read', false)
            ->count();
    }


}
