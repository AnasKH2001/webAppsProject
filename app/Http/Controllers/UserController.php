<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createEmployee(Request $request)
    {
        $creator = $request->user(); 
        try {
            $employee = $this->userService->createEmployee($request->all(), $creator);
            return response()->json($employee, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 403); // Forbidden
        }
    }
}
