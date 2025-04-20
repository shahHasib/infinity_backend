<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('status','approved')->get();
        return response()->json([
            'status' => true,
            'message' => "All Post Data",
            'data' => $posts
        ]);
    }
   

    public function store(Request $request)
{
    if (!Auth::check() || Auth::user()->role !== 'creator'&& Auth::user()->role !== 'admin') {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized access',
        ], 403);
    }

    $validateUser = Validator::make($request->all(), [
        'title' => 'required',
        'description' => 'required',
        'author' => 'required',
        'category' => 'required',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validateUser->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation Error',
            'errors' => $validateUser->errors()->all()
        ], 401);
    }

    $imagePath = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('uploads'), $imageName);
        $imagePath = asset('uploads/' . $imageName);
    }

    $post = Post::create([
        'user_id' => Auth::user()->id,
        'title' => $request->title,
        'description' => $request->description,
        'author' => $request->author,
        'category' => $request->category,
        'status' => $request->status ?? 'pending',
        'image' => $imagePath
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Post created successfully',
        'post' => $post,
    ], 200);
}

    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Post details',
            'post' => $post,
        ], 200);
    }
    public function showCategory($category) {
        $posts = Post::where('category', $category)->where('status','approved')->get(); // Fetch posts
    
        return response()->json([
            'status' => true,
            'posts' => $posts, // Return actual data
        ], 200);
    }
    

    public function update(Request $request, string $id)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['creator', 'admin'])) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }
        

        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image && file_exists(public_path($post->image))) {
                unlink(public_path($post->image));
            }

            // Upload new image
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads'), $imageName);
            $post->image = 'uploads/' . $imageName;
        }

        $post->update($request->only(['title', 'description', 'author', 'category', 'status']));

        return response()->json([
            'status' => true,
            'message' => 'Post updated',
            'post' => $post,
        ], 200);
    }

    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        // Delete image if exists
        if ($post->image && file_exists(public_path($post->image))) {
            unlink(public_path($post->image));
        }

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post deleted'
        ], 200);
    }
}
