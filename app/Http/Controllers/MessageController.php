<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Merr të gjitha mesazhet
     */
    public function index()
    {
        $messages = Message::latest()->get();
        return response()->json($messages);
    }

    /**
     * Ruaj një mesazh të ri nga Contact.jsx
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $msg = Message::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Mesazhi u dërgua me sukses!',
            'data' => $msg,
        ], 201);
    }

    /**
     * Merr një mesazh të vetëm me ID
     */
    public function show(Message $message)
    {
        return response()->json($message);
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->json(['message' => 'Mesazhi u fshi me sukses!']);
    }
}
