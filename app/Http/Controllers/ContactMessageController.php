<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    // Store a new message
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'replied' => false, // Default value
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully!',
        ], 200);
    }

    // Fetch all messages for admin
    public function index()
    {
        $messages = ContactMessage::latest()->get();
        return response()->json($messages);
    }

    // Fetch a single message
    public function show($id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        return response()->json($message);
    }

    // Reply to a message
    public function reply(Request $request, $id)
{
    $request->validate([
        'reply_message' => 'required',
    ]);

    $message = ContactMessage::find($id);
    if (!$message) {
        return response()->json(['error' => 'Message not found'], 404);
    }

    // Email data
    $emailData = [
        'name' => $message->name,
        'email' => $message->email,
        'replyMessage' => $request->reply_message,
    ];

    // Send email using Blade template
    Mail::send('emails.reply', $emailData, function ($mail) use ($message) {
        $mail->to($message->email)
             ->subject("Reply from Admin")
             ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    });

    // Mark message as replied
    $message->replied = true;
    $message->save();

    return response()->json([
        'message' => 'Reply sent successfully',
        'replied' => true, // Frontend will use this to update the button
    ]);
}

    // Delete a message
    public function destroy($id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully']);
    }
}
