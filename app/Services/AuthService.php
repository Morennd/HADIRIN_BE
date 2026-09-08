<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register user baru.
     */
    public function register(array $data)
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data' => [
                'user'  => $user,
                'token' => $token,
            ],
        ];
    }

    /**
     * Login user.
     */
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return [
                'success' => false,
                'message' => 'Email atau password salah.',
            ];
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user'  => $user,
                'token' => $token,
            ],
        ];
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => 'Logout berhasil.',
        ];
    }

    /**
     * Ambil data user yang sedang login.
     */
    public function me(Request $request)
    {
        return [
            'success' => true,
            'data' => $request->user(),
        ];
    }
}