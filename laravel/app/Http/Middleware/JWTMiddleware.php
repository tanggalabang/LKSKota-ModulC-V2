<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Helpers\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        // dd($token);
        $decoded = JWTAuth::decodeJWT($token, env('JWT_SECRET'));

        // check if fails
        if (!$decoded) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Add user to Auth instance
        $user = User::find($decoded->sub);

        $request->merge(['user' => $user]);

        // Continue with the request
        return $next($request);
    

       
    }
}
