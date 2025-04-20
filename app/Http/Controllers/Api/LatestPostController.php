<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

class LatestPostController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'approved')
            ->orderBy('created_at', 'desc') // Newest first
            ->get();

        return response()->json([
            'status' => true,
            'message' => "Latest Posts",
            'data' => $posts
        ]);
    }
}
