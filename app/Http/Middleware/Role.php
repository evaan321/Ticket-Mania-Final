<?php
//
//namespace App\Http\Middleware;
//
//use Closure;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Log;
//
//class Role
//{
//    public function handle(Request $request, Closure $next, $role)
//    {
//        if (Auth::check()) {
//            $user = Auth::user();
//
//            Log::info('Role Middleware Check', [
//                'user_id' => $user->id,
//                'user_role' => $user->role,
//                'expected_role' => $role,
//            ]);
//            // Check if role exists and matches
//            if ($user->role && $user->role === $role) {
//                return $next($request);
//            }
//        }
//
//        return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
//    }
//}
