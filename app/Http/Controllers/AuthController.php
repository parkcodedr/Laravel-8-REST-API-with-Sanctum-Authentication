<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8|confirmed|string'

        ]);
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);
        $token =  $user->createToken('authtoken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function signin(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string'

        ]);
        //check email
        $user = User::where('email', $fields['email'])->first();

        //check passwords
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'wrong credentials'], 401);
        }

        $token =  $user->createToken('authtoken')->plainTextToken;
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(User $user)
    {
        auth()->user()->tokens()->delete();
        return [
            'messages' => 'logged out'
        ];
    }
}
