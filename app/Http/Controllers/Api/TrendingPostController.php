<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;

class TrendingPostController extends Controller
{
    public function index()
    {
        $posts = Post::withCount(['likes', 'comments'])
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->subDays(1)) // Last 1 days
            ->orderByRaw('(likes_count + comments_count) DESC') // Sort by engagement
            ->get();

        return response()->json([
            'status' => true,
            'message' => "Trending Posts",
            'data' => $posts
        ]);
    }
}
