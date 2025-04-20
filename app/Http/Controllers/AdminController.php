<?php

namespace App\Http\Controllers;

use App\Mail\NewPostNotification;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    // Fetch dashboard data
    public function index(Request $request)
    {
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalPosts = Post::count();
        $totalComments = Comment::count();
        $recentUsers = User::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->paginate(5);
        $pendingPosts = Post::where('status', 'pending')->get();

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalPosts' => $totalPosts,
            'totalComments' => $totalComments,
            'recentUsers' => $recentUsers,
            'pendingPosts' => $pendingPosts,
        ]);
    }
    public function adminPosts(Request $request) {
        $query = Post::query();
    
        // 🔍 Search by title or description
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
    
        // 📂 Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
    
        // 🚦 Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
    
        // 🔄 Sort by status (approved first)
        $posts = $query->orderBy('status', 'desc')->get();
    
        return response()->json([
            'status' => true,
            'message' => "Filtered Post Data",
            'data' => $posts
        ]);
    }
     // Handle post actions: approve/reject
    public function handlePostAction(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->status = $request->input('status');
        $post->save();
        // Send email notification to all subscribers
        $subscribers = Subscription::pluck('email'); // Get all subscriber emails

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber)->send(new NewPostNotification($post));
        }
        return response()->json(['message' => 'Post status updated successfully.Notified to the subscribers on Gmail']);
    }

    // Ban user
    public function banUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->banned = true;
        $user->ban_reason = $request->input('reason');
        $user->save();

        return response()->json(['message' => 'User banned successfully']);
    }

    // Unban user
    public function unBanUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->banned = false;
        $user->ban_reason = null;
        $user->save();

        return response()->json(['message' => 'User unbanned successfully']);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    // Fetch reports
    public function getReports()
    {
        $postsReport = Post::with('user')->get();
        $usersReport = User::all();

        return response()->json([
            'postsReport' => $postsReport,
            'usersReport' => $usersReport,
        ]);
    }
}
