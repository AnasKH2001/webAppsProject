<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $result = $this->auth->register($request->all());
        return response()->json($result);
    }

    /**
     * Handle user login
     */
    public function login(Request $request)
    {
        $result = $this->auth->login($request->all());
        return response()->json($result);
    }

    public function logout(Request $request)
    {
        // Delete the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }


    public function verifyOtp(Request $request)
    {
        return response()->json($this->auth->verifyOtp($request->all()));
    }

    public function resendOtp(Request $request)
    {
        return response()->json($this->auth->resendOtp($request->all()));
    }
}
