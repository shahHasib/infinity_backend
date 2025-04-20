<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Show like status and count for a post
    public function index($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        $likeCount = Like::where('post_id', $id)->count();
        $likedByUser = $user ? Like::where('post_id', $id)->where('user_id', $user->id)->exists() : false;

        return response()->json([
            'likes' => $likeCount,
            'liked' => $likedByUser,
        ]);
    }

    // Like or unlike a post
    public function store(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $post = Post::findOrFail($id);

        $like = Like::where('post_id', $id)->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'post_id' => $id,
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        $likeCount = Like::where('post_id', $id)->count();

        return response()->json([
            'likes' => $likeCount,
            'liked' => $liked,
        ]);
    }

    // Get all likes for a post
    public function getLikes($postId)
    {
        $likes = Like::where('post_id', $postId)->with('user:id,name')->get();
        return response()->json($likes);
    }
}
