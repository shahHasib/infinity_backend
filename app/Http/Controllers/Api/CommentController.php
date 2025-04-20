<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display all comments for a specific post.
     */
    public function index($id)
    {
        $post = Post::findOrFail($id);
        $comments = $post->comments()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ], 200);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $post = Post::findOrFail($id);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully!',
            'comment' => $comment
        ], 201);
    }

    /**
     * Display a specific comment.
     */
    public function show($id)
    {
        $comment = Comment::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $comment
        ], 200);
    }
    public function getComments($postId)
    {
        $post = Post::findOrFail($postId);
        $comments = $post->comments()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ], 200);
    }

    /**
     * Update a comment.
     */
    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully!',
            'comment' => $comment
        ], 200);
    }

    /**
     * Remove a comment.
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $user = Auth::user();

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully!'
        ], 200);
    }
}
