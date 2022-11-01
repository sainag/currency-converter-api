<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {
            $fields = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|min:6|confirmed'
            ]);

            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
            ]);

            $token = $user->createToken('currencyconvertertoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to register'
            ], 500);
        }
    }

    public function login(Request $request) {
        try {
            $fields = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string'
            ]);

            //check email
            $user = User::where('email', $fields['email'])->first();

            //check password
            if(!$user || !Hash::check($fields['password'], $user->password)) {
                return response(['message' => 'Invalid login'], 401);
            }

            $token = $user->createToken('currencyconvertertoken')->plainTextToken;

            $response = [
                'user' => $user,
                'token' => $token
            ];

            return response($response, 201);
        } catch (Throwable $e) {
            return response([
                'message' => 'Unable to login'
            ], 500);
        }
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();
        return ['message' => 'Logged out'];
    }
}
