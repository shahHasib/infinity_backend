<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function getUserProfile()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        return response()->json(['user' => $user, 'profile' => $profile]);
    }

    /**
     * Get creator statistics (posts, likes, comments count).
     */
    public function getCreatorProfile()
    {
        $user = Auth::user();
        
        // Optimize query performance
        $postIds = Post::where('user_id', $user->id)->pluck('id');

        $stats = [
            'posts' => $postIds->count(),
            'likes' => Like::whereIn('post_id', $postIds)->count(),
            'comments' => Comment::whereIn('post_id', $postIds)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Upgrade user to Creator role.
     */
    public function becomeCreator(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        if ($user->role === 'creator') {
            return response()->json(['success' => false, 'message' => 'You are already a creator!']);
        }

        // Validate input
        $request->validate([
            'bio' => 'required|string',
            'social_link' => 'required|url',
            'website' => 'required|url',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle profile picture upload securely
        $imagePath = null;
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads'), $imageName);
            $imagePath = asset('uploads/' . $imageName);
        }
        
        // Update user role
        $user->role = 'creator';
        $user->save();
        


        // Store creator profile info
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                
                'bio' => $request->bio,
                'social_link' => $request->social_link,
                'website' => $request->website,
                'profile_picture' => $imagePath,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'You are now a creator!',
            'role' => $user->role,
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
{
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    // Validate inputs
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'bio' => 'nullable|string',
        'social_link' => 'nullable|url',
        'website' => 'nullable|url',
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Update user basic info
    $user->update([
        'name' => $validatedData['name'],
        'email' => $validatedData['email']
    ]);

    // Ensure profile exists
    $profile = Profile::firstOrCreate(['user_id' => $user->id]);

    // Update profile fields
    $profile->bio = $validatedData['bio'] ?? $profile->bio;
    $profile->social_link = $validatedData['social_link'] ?? $profile->social_link;
    $profile->website = $validatedData['website'] ?? $profile->website;

    // Handle profile picture update (using public/uploads)
    $imagePath = $profile->profile_picture; // Keep existing path if no new file is uploaded

    if ($request->hasFile('profile_picture')) {
        // Delete old image if it exists
        if ($profile->profile_picture) {
            $oldImagePath = public_path(str_replace(asset('/'), '', $profile->profile_picture));
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Store new image
        $image = $request->file('profile_picture');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads'), $imageName);
        $imagePath = asset('uploads/' . $imageName);
    }

    // Update profile picture path
    $profile->profile_picture = $imagePath;
    $profile->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'user' => $user,
        'profile' => $profile,
    ]);
}

    /**
     * Show all posts of the authenticated creator.
     */
    public function showCreatorPost()
    {
        $posts = Post::where('user_id', Auth::id())->get();
        return response()->json($posts);
    }
}
