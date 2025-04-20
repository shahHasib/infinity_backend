<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\View; 
use Illuminate\Http\Request;
use Carbon\Carbon;


class StatsController extends Controller
{
    public function getStats()
    {
        // Active Readers: Count users who have viewed posts in the last 30 days
        $activeReaders = User::
       // whereHas('', function($query) {
        //    $query->
            where('created_at', '>=', Carbon::now()->subDays(30))
       // })
        ->count();

        // Content Creators: Count distinct users who have published posts
        $contentCreators = Post::distinct('user_id')->count('user_id');

        // Monthly Views: Count views in the current month
        $monthlyViews = View::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->count();

        // Return stats as JSON
        return response()->json([
            'active_readers' => $activeReaders,
            'content_creators' => $contentCreators,
            'monthly_views' => $monthlyViews,
        ]);
    }
}
