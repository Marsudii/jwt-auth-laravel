<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{


    public function all()
    {
        $dataUsers = User::all();

        return response()->json([
            'status' => "OK",
            'messages' => "Successfully Load Data",
            'data' => $dataUsers
        ]);
    }
}
