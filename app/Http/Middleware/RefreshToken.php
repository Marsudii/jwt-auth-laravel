<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $refreshToken = $request->only('refresh_token');

            $decodeRefreshToken = JWT::decode($refreshToken['refresh_token'], new Key(env("JWT_SECRET_REFRESH_TOKEN"), 'HS256'));

            // dd($decodeRefreshToken);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'Token Tidak Valid',
                ]
                ,
                401
            );
        }

    }
}
