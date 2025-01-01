<?php

namespace App\Services;

use App\Models\User;
use App\Models\Customers;
use Kreait\Firebase\Messaging;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    
    public function sendNotification(string $token, string $title, string $body, array $data = [], int $customerId)
    {
        try {
            // Create the notification in the database
            $notification = \App\Models\Notification::create([
                'customer_id' => $customerId, 
                'title' => $title, 
                'body' => $body, 
                'data' => !empty($data) ? json_encode($data) : null,
            ]);
    
            // Create Firebase Cloud Messaging Notification
            $firebaseNotification = Notification::create($title, $body);
    
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($firebaseNotification);
    
            if (!empty($data)) {
                $message = $message->withData($data);
            }
    
            // Send the notification via Firebase 
            $this->messaging->send($message);
    
            return $notification;  // Optionally return the notification record
    
        } catch (\Exception $e) {
            Log::error('Firebase notification error: ' . $e->getMessage());
            throw $e;
        } 
    }
    

    public function sendMulticast(array $tokens, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withNotification($notification);

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            return $this->messaging->sendMulticast($message, $tokens);
        } catch (\Exception $e) {
            \Log::error('Firebase multicast error: ' . $e->getMessage());
            throw $e;
        }
    }


    public function sendNotificationToAllDevices(Customers $customer, string $title, string $body, array $data = [])
{
    $tokens = array_filter([$customer->fcm_token, $customer->web_fcm_token]);
    
    if (!empty($tokens)) {
        return $this->sendMulticast($tokens, $title, $body, $data);
    }
    
    return null;
}
}