<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */

     public function handle(Request $request, Closure $next, string ...$roles): Response
     {
         if (!Auth::check()) {
             return response()->json(['message' => 'User not authenticated'], 401);
         }
     
         $user = Auth::user();
         
         Log::info('Authenticated User:', ['role' => $user->role]);
     
         // Allow multiple roles (admin & creator)
         if (!in_array($user->role, $roles)) {
             return response()->json([
                 'message' => 'Unauthorized',
                 'expected_roles' => $roles,
                 'actual_role' => $user->role
             ], 403);
         }
     
         return $next($request);
     }
     
    
}
