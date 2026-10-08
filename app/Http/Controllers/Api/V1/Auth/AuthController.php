<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ApiResponseService;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected ApiResponseService $response
    ) {
    }

    /**
     * Register.
     */
    public function register(
        RegisterRequest $request
    ): JsonResponse {
        $result = $this->authService->register(
            $request->validated()
        );

        return $this->response->success(
            'Registration successful.',
            $result,
            201
        );
    }

    /**
     * Login.
     */
    public function login(
        LoginRequest $request
    ): JsonResponse {
        $user = User::where(
            'email',
            $request->email
        )->first();

        if (
            !$user ||
            !Hash::check(
                $request->password,
                $user->password
            )
        ) {
            return $this->response->error(
                'Invalid email or password.',
                null,
                401
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove previous tokens
        |--------------------------------------------------------------------------
        */

        //$user->tokens()->delete();

        /*
        |--------------------------------------------------------------------------
        | Create new token
        |--------------------------------------------------------------------------
        */

        $token = $user
            ->createToken('tax-filing-api')
            ->plainTextToken;

        /*
        |--------------------------------------------------------------------------
        | Load organisations
        |--------------------------------------------------------------------------
        */

        $user->load([
            'organisations',
            'roles.permissions',
        ]);

        return $this->response->success(
            'Login successful.',
            [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        );
    }

    /**
     * Logout.
     */
    public function logout(
        Request $request
    ): JsonResponse {
        $request
            ->user()
            ->currentAccessToken()
            ?->delete();

        return $this->response->success(
            'Logout successful.'
        );
    }

    /**
     * Current authenticated user.
     */
    public function me(
        Request $request
    ): JsonResponse {

        //dd($request->user());
        $user = $request
            ->user()
            ->load([
                'organisations',
                'roles.permissions',
            ]);

        return $this->response->success(
            'User retrieved successfully.',
            [
                'user' => $user,
            ]
        );
    }
}