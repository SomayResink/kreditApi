<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'tgl_lahir'  => ['required', 'date'],
            'alamat'     => ['required', 'string'],
            'no_hp'      => ['required', 'numeric'],
            'foto_ktp'   => ['required', 'string'], // kalau nanti upload file bisa diubah jadi file upload
            'role'       => ['in:user,admin'], // default user, admin hanya bisa lewat seeder
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'tgl_lahir'  => $validated['tgl_lahir'],
            'alamat'     => $validated['alamat'],
            'no_hp'      => $validated['no_hp'],
            'foto_ktp'   => $validated['foto_ktp'],
            'role'       => $validated['role'] ?? 'user', // default user
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Optional: revoke old tokens
        // $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
}
