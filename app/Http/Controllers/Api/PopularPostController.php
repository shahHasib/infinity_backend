<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

class PopularPostController extends Controller
{
    public function index()
    {
        $posts = Post::withCount('likes')
            ->where('status', 'approved')
            ->orderBy('likes_count', 'desc') // Sort by most liked
            ->get();

        return response()->json([
            'status' => true,
            'message' => "Popular Posts",
            'data' => $posts
        ]);
    }
}
