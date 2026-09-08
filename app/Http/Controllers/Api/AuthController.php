<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    /**
     * Constructor.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register user baru.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json($result, 201);
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (!$result['success']) {
            return response()->json($result, 401);
        }

        return response()->json($result, 200);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $result = $this->authService->logout($request);

        return response()->json($result, 200);
    }

    /**
     * Menampilkan data user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $result = $this->authService->me($request);

        return response()->json($result, 200);
    }
}