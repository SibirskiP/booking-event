<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Registracija (public) - default role = customer
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // expect password_confirmation
            'phone'    => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => 'customer' // default - ne dozvoljavamo da se sami registruju kao admin/organizer
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'token' => $token
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'token' => $token
        ], 200);
    }

    // Logout (current token)
    public function logout(Request $request)
    {
        $user = $request->user();
        // obriši samo trenutni token
        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    // Current user
    public function me(Request $request)
    {
        return response()->json(['data' => $request->user()], 200);
    }
}
