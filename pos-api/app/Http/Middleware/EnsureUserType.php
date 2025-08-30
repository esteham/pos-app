<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$types): Response
    {

        $user = $request->user();
        
        if(!$user || !in_array($user->user_type, $types))
        {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        return $next($request);

    }// end method
}
