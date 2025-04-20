<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:subscriptions']);

        Subscription::create(['email' => $request->email]);

        return response()->json(['message' => 'Subscribed successfully!']);
    }

    public function notifySubscribers($post)
    {
        $subscribers = Subscription::all();
        foreach ($subscribers as $subscriber) {
            Mail::raw("New post published: {$post->title}", function ($message) use ($subscriber) {
                $message->to($subscriber->email)
                    ->subject("New Blog Post Alert!");
            });
        }
    }
}
