<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status === "banned") {
            $query->whereNotNull('ban_reason');
        } elseif ($request->status === "active") {
            $query->whereNull('ban_reason');
        }

        return response()->json($query->paginate(10));
    }

    public function destroy($id)
    {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        $user->delete(); // This will permanently delete. Use softDeletes if needed.
    
        return response()->json(['message' => 'User deleted successfully.']);
    }
    

}
