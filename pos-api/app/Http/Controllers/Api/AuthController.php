<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'   => 'required|string',
            'password'=> 'required|string|min:6',
        ]);

        $login = $data['login'];

        //Login fetch
        if (filter_var($login, FILTER_VALIDATE_EMAIL)){
            $user = User::where('email', $login)->first();
        }
        else {
            $user = User::where('phone', $login)->first();
        }

        //if not user or password
        if (!$user || !Hash::check($data['password'], $user->password)){
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        //Create token
        $tokenResult = $user->createToken('pos-token');
        $token = $tokenResult->plainTextToken;

        //Optional (token expiry set)
        // $user->tokens()->latest()->first()->update([
        //     'expires_at' => now()->addHours(24),
        // ]);


        return response()->json([
            'token' => $token,
            'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                ]
        ]);

    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
