<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class AdminCommentController extends Controller
{
    // Get all comments with pagination
    public function index(Request $request)
    {
        $comments = Comment::with('post', 'user')->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($comments);
    }

    // Approve a comment
    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'approved';
        $comment->save();
        return response()->json(['message' => 'Comment approved successfully']);
    }

    // Reject a comment
    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->status = 'rejected';
        $comment->save();
        return response()->json(['message' => 'Comment rejected successfully']);
    }

    // Delete a comment
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
