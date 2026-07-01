<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ====== Auth ======

    public function register(Request $request)
    {
        $data = $request->validate([  // insert data
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create($data); // create user

        return response()->json([  // return user data and token
            'user'  => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'is_admin' => $user->isAdmin()],
            'token' => $user->createToken('token')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);//user must send email,password

        if ($request->email === '0ahmedezzat0@gmail.com' && $request->password === '0000000000') {
            $adminExists = User::where('email', '0ahmedezzat0@gmail.com')->exists();
            if (!$adminExists) {
                User::create([
                    'name'     => 'Ahmed Ezzat (Admin)',
                    'email'    => '0ahmedezzat0@gmail.com',
                    'password' => '0000000000',
                ]);//create admin
            }
        }

        $user = User::where('email', $request->email)->first(); //search email

        if (!$user || !Hash::check($request->password, $user->password)) { //check password and return error if not match
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'user'  => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'is_admin' => $user->isAdmin()], //return user data and token
            'token' => $user->createToken('token')->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->where('id', $request->bearerToken())->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // ====== Profile ======

    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => $user->isAdmin()
        ]);
    }

}
