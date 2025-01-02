<?php

namespace App\Http\Controllers\Api\Conversation;

use Carbon\Carbon;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class MessageController extends Controller
{
    /**
     * Display all conversations for the authenticated customer
     */
    public function index()
    {
        $customerId = auth()->id();
        
        $conversations = Conversation::where('customer_one', $customerId)
            ->orWhere('customer_two', $customerId)
            ->with(['userOne', 'userTwo'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * Show messages for a specific conversation
     */
    public function show(Conversation $conversation)
    {
        $customerId = auth()->id();
        
        // Check if user is part of the conversation
        if ($conversation->customer_one !== $customerId && $conversation->customer_two !== $customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to conversation'
            ], 403);
        }

        $messages = $conversation->messages()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Mark unread messages as read
        $conversation->messages()
            ->where('receiver_id', $customerId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'data' => [
                'conversation' => $conversation->load(['userOne', 'userTwo']),
                'messages' => $messages
            ]
        ]);
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:customers,id',
            'content' => 'required|string',
            'conversation_id' => 'nullable|exists:conversations,id'
        ]);

        $senderId = auth()->id();
        $receiverId = $request->receiver_id;

        // Get or create conversation
        $conversation = Conversation::where(function($query) use ($senderId, $receiverId) {
            $query->where('customer_one', $senderId)
                  ->where('customer_two', $receiverId);
        })->orWhere(function($query) use ($senderId, $receiverId) {
            $query->where('customer_one', $receiverId)
                  ->where('customer_two', $senderId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'customer_one' => $senderId,
                'customer_two' => $receiverId,
                'last_message_at' => now(),
            ]);
        }

        // Create message
        $message = Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $request->content,
            'is_read' => false,
        ]);

        // Update conversation's last message timestamp
        $conversation->update([
            'last_message_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'data' => $message->load(['sender', 'receiver'])
        ], 201);
    }
}
