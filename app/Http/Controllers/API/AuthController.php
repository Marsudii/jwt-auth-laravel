<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\RefreshToken;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'email.required' => "Email Wajib Diisi !!",
                'email.email' => "Email Tidak Valid !!",
                'password.required' => 'Password Wajib Diiisi!!'
            ]
        );

        //VALIDASI FORM REQUEST
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'messages' => 'Bad Request',
                'errors' => $validator->errors()
            ]);
        }

        $dataFormRequest = $validator->validated();
        //VALIDASI EMAIL 
        if (!User::where('email', $dataFormRequest['email'])->exists()) {
            return response()->json([
                'status' => false,
                'messages' => 'Invalid Email',
                'errors' => [
                    'email' => "Email Not Found"
                ]
            ], 404);
        }
        // VALIDASI EMAIL DAN PASSWORD

        if (Auth::attempt($dataFormRequest)) {



            $refreshTokenDb = RefreshToken::where('user_id', Auth::user()->id)->first();
            if ($refreshTokenDb) {
                return response()->json([
                    'status' => true,
                    'messages' => 'Anda Sudah Login',

                ]);
            }
            $accesToken = JWT::encode([
                'user_id' => Auth::user()->id,
                'email' => Auth::user()->email,
                'exp' => time() + 60

            ], env('JWT_SECRET_ACCESS_TOKEN'), 'HS256');

            $refreshToken = JWT::encode([
                'user_id' => Auth::user()->id,
                'email' => Auth::user()->email,
                'exp' => null
            ], env('JWT_SECRET_REFRESH_TOKEN'), 'HS256');

            RefreshToken::create([
                'user_id' => Auth::user()->id,
                'refresh_token' => $refreshToken
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Login Success',
                'data' => [
                    'user' => [
                        'user_id' => Auth::user()->id,
                        'email' => Auth::user()->email
                    ],
                    'auth' => [
                        'access_token' => $accesToken,
                        'refresh_token' => $refreshToken
                    ]
                ]
            ]);


        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid Credentials',
            'errors' => [
                'password' => "password wrong"
            ]
        ]);






    }


    public function logout(Request $request)
    {

        $token = $request->bearerToken();

        $decodeToken = JWT::decode($token, new Key(env('JWT_SECRET_ACCESS_TOKEN'), 'HS256'));

        $userID = RefreshToken::where('user_id', $decodeToken->user_id)->first();




        if ($userID) {
            $userID->delete();
            return response()->json([
                'success' => true,
                'messages' => 'Logout Succeess'
            ]);
        }
        return response()->json([
            'success' => false,
            'messages' => 'Token Not Found'
        ]);

    }


    public function refreshToken(Request $request)
    {

        $requestToken = $request->input('refresh_token');
        $refeshTokenDB = RefreshToken::where('refresh_token', $requestToken)->first();

        if ($refeshTokenDB == null) {
            return response()->json([
                'status' => false,
                'messages' => 'Refresh Token Not Found'
            ], 401);
        }

        $userDb = User::where('id', $refeshTokenDB->user_id)->first();
        $accesToken = JWT::encode([
            'user_id' => $userDb->id,
            'email' => $userDb->email,
            'exp' => time() + 60

        ], env('JWT_SECRET_ACCESS_TOKEN'), 'HS256');

        return response()->json([
            'status' => true,
            'messages' => 'Access Token Created',
            'data' => [
                'access_token' => $accesToken
            ]
        ], 401);
    }

}
