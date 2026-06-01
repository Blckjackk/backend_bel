<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $chats = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with(['sender', 'receiver', 'office'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($chats);
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'office_id' => 'nullable|exists:offices,id',
            'message' => 'required|string',
        ]);

        $chat = Chat::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'office_id' => $request->office_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json($chat, 201);
    }

    public function getConversation($userId)
    {
        $authId = auth()->id();
        
        $chats = Chat::where(function($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })->orWhere(function($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Chat::where('sender_id', $userId)->where('receiver_id', $authId)->update(['is_read' => true]);

        return response()->json($chats);
    }
}
