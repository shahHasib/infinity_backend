<?php

use App\Http\Controllers\AdminCommentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LatestPostController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\PopularPostController;
use App\Http\Controllers\Api\PostController;

use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TrendingPostController;
use App\Http\Controllers\ContactMessage;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/user', [AuthController::class, 'user']);
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Public Routes (Anyone can access)
Route::get('/show/{id}', [PostController::class, 'show']); // View a single post
Route::get('/index', [PostController::class, 'index']); // View all posts
Route::get('/post/{category}', [PostController::class, 'showCategory']); // View posts by category
Route::get('/search/{keyword}', [PostController::class, 'search']); // Search for posts
Route::get('/posts/{postId}/likes', [LikeController::class, 'getLikes']);
Route::get('/posts/{postId}/comments', [CommentController::class, 'getComments']);
Route::post('/contact', [ContactMessageController::class, 'store']);
Route::post('/subscribe', [SubscriptionController::class, 'subscribe']);

Route::middleware('auth:sanctum')->get('/admin/reports', [ReportController::class, 'index']);

Route::get('/showcomment/{id}', [CommentController::class, 'index']);
// Route::post('/share/{id}', [ShareController::class, 'store']); // Share a post
Route::get('/reports/{type}/pdf', [ReportController::class, 'generatePDF']);
Route::get('/reports/{type}/excel', [ReportController::class, 'generateExcel']);
Route::get('/stats', [StatsController::class, 'getStats']);
Route::get('/trending-posts', [TrendingPostController::class, 'index']);
Route::get('/latest-posts', [LatestPostController::class, 'index']);
Route::get('/popular-posts', [PopularPostController::class, 'index']);

// Protected Routes (Require Authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/store', [PostController::class, 'store'])->middleware('role:creator,admin'); // Only creators & admins can create posts
    Route::post('post/update/{id}', [PostController::class, 'update'])->middleware('role:creator,admin'); // Only creators & admins can update posts
    Route::delete('post/delete/{id}', [PostController::class, 'destroy'])->middleware('role:creator,admin'); // Only admins can delete posts
    Route::post('/like/{id}', [LikeController::class, 'store']); // Like a post
    Route::get('/showlike/{id}', [LikeController::class, 'index']);
    Route::post('/comment/{id}', [CommentController::class, 'store']); // Comment on a post

    // Route::post('/follow/{id}', [FollowerController::class, 'store']); // Follow a user
});


// Admin Routes (Require Admin Role)
Route::middleware('auth:sanctum', 'role:admin')->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'index']); // View dashboard
    Route::delete('admin/users/{id}', [AdminController::class, 'destroy']); // Delete a user
    Route::put('admin/posts/{id}/status', [AdminController::class, 'handlePostAction']); // Approve or reject a post
    Route::post('/admin/users/{id}/ban', [AdminController::class, 'banUser']);
    Route::post('/admin/users/{id}/unban', [AdminController::class, 'unBanUser']);
    Route::get('/admin/settings', [AdminSettingsController::class, 'getSettings']);
    Route::put('/admin/settings', [AdminSettingsController::class, 'updateSettings']);
    Route::put('/admin/updateProfile', [AdminSettingsController::class, 'updateProfile']);
   // Route::get('/admin/reports', [AdminController::class, 'getReports']);
    Route::get('/admin/posts', [AdminController::class, 'adminPosts']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

Route::get('/users', [AdminUserController::class, 'index']);
//Route::post('/users/ban/{id}', [AdminUserController::class, 'ban']);
//Route::post('/users/unban/{id}', [AdminUserController::class, 'unban']);

});
Route::get('/admin/comments', [AdminCommentController::class, 'index']);
Route::post('/admin/comments/approve/{id}', [AdminCommentController::class, 'approve']);
Route::post('/admin/comments/reject/{id}', [AdminCommentController::class, 'reject']);
Route::delete('/admin/comments/{id}', [AdminCommentController::class, 'destroy']);
Route::get('/admin/settings', [AdminSettingsController::class, 'index']);
Route::post('/admin/update-settings', [AdminSettingsController::class, 'update']);

Route::get('/admin/messages', [ContactMessageController::class, 'index']);
Route::get('/admin/messages/{id}', [ContactMessageController::class, 'show']);
Route::delete('/admin/messages/{id}', [ContactMessageController::class, 'destroy']);
Route::post('/admin/messages/{id}/reply', [ContactMessageController::class, 'reply']); 
// Profile Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getUserProfile']);
    Route::post('/become-creator', [ProfileController::class, 'becomeCreator']);
    Route::get('/creator-profile', [ProfileController::class, 'getCreatorProfile']);
    Route::post('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::get('/profile/posts', [ProfileController::class, 'showCreatorPost']);
});
