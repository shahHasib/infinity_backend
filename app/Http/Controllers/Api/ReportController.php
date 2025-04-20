<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $totalPosts = Post::count();
        $totalUsers = User::count();
        $totalComments = Comment::count();
       // $totalReports = Report::count(); // ✅ Count total reported content
    
        // Last 7 days of posts
        $dailyPosts = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();
    
        // Last 7 days of user signups
        $dailyUsers = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();
    
        // Top 5 contributors (sorted by posts & comments count)
        $topContributors = User::withCount(['posts', 'comments'])
            ->orderByDesc('posts_count')
            ->orderByDesc('comments_count')
            ->take(5)
            ->get(['id', 'name', 'posts_count', 'comments_count']);
    
        // Reported content details
        // $reportedContent = Report::with(['post', 'user']) // ✅ Eager loading for performance
        //     ->orderByDesc('created_at')
        //     ->take(10)
        //     ->get()
        //     ->map(function ($report) {
        //         return [
        //             'id' => $report->id,
        //             'content' => optional($report->post)->content ?? 'Deleted Post',
        //             'reportedBy' => $report->user->name ?? 'Unknown',
        //             'reason' => $report->reason,
        //             'created_at' => $report->created_at->format('Y-m-d'),
        //         ];
        //     });
    
        return response()->json([
            'totalPosts' => $totalPosts,
            'totalUsers' => $totalUsers,
            'totalComments' => $totalComments,
           // 'totalReports' => $totalReports, // ✅ Added missing totalReports
            'dailyPosts' => $dailyPosts,
            'dailyUsers' => $dailyUsers,
            'topContributors' => $topContributors,
           // 'reportedContent' => $reportedContent,
        ]);
    }
    
    // Generate PDF Report
    public function generatePDF(Request $request, $type)
    {
        $filteredData = $this->getFilteredData($request, $type);

        // Extract data
        $data = $filteredData['data'];
        $totalUsers = $filteredData['totalUsers'];
        $totalComments = $filteredData['totalComments'];
        $totalLikes = $filteredData['totalLikes'];

        // Load the correct PDF view and download
        $pdf = Pdf::loadView("reports.$type", compact('data', 'totalUsers', 'totalComments', 'totalLikes'));
        return $pdf->download($type . '_report.pdf');
    }

    // Fetch filtered data
    private function getFilteredData(Request $request, $type)
    {
        switch ($type) {
            case 'posts':
                $query = Post::with('user') // Fetch user (author)
                    ->select('posts.*')
                    ->selectSub(function ($query) {
                        $query->from('likes')
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('likes.post_id', 'posts.id');
                    }, 'likes_count')
                    ->selectSub(function ($query) {
                        $query->from('comments')
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('comments.post_id', 'posts.id');
                    }, 'comments_count');

                if ($request->has('user_id')) {
                    $query->where('user_id', $request->user_id);
                }
                break;

            case 'users':
                $query = User::query()
                    ->select('users.*')
                    ->selectSub(function ($query) {
                        $query->from('posts')
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('posts.user_id', 'users.id');
                    }, 'posts_count')
                    ->selectSub(function ($query) {
                        $query->from('comments')
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('comments.user_id', 'users.id');
                    }, 'comments_count')
                    ->selectSub(function ($query) {
                        $query->from('likes')
                            ->selectRaw('COUNT(*)')
                            ->whereColumn('likes.user_id', 'users.id');
                    }, 'likes_count');
                break;

            default:
                return ['data' => collect(), 'totalUsers' => 0, 'totalComments' => 0, 'totalLikes' => 0];
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        // Fetch data and totals
        $data = $query->get();
        $totalUsers = User::count();
        $totalComments = Comment::count();
        $totalLikes = Like::count();

        return compact('data', 'totalUsers', 'totalComments', 'totalLikes');
    }
}
