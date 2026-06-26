<?php

namespace App\Modules\Authentication\Controllers\V1;

use App\Core\BaseController;
use App\Modules\Authentication\Requests\LoginRequest;
use App\Modules\Authentication\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Handle incoming login request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse(
            $result,
            'Login successful'
        );
    }

    /**
     * Handle incoming logout request.
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(
            null,
            'Logged out successfully'
        );
    }
}
