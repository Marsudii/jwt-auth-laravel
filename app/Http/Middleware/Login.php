<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            $decodeToken = JWT::decode($token, new Key(env('JWT_SECRET_ACCESS_TOKEN'), 'HS256'));



            return $next($request);


        } catch (\Exception $e) {
            return response()->json(
                [
                    'message' => 'gagal',

                ]
                ,
                401
            );
        }




    }
}
