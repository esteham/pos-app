<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserType
{
  
    public function handle(Request $request, Closure $next, ...$types)
    {
        $user = $request->user();
        if(!$user || !in_array($user->user_type, $types))
        {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
