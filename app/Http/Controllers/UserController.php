<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;

class UserController extends Controller
{

/**
 * 
 * 
 * /**
 * @OA\Info(
 *     title="Your API Title",
 *     version="1.0.0",
 *     description="Login API",
 *     @OA\Contact(
 *         email="saurabh"
 *     ),
 *     @OA\License(
 *         name="MIT"
 *     )
 * )


* @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     @OA\Parameter(
     *         name="saurabh jadhav",
     *         in="query",
     *         description="Saurabh",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="saurabhjadhav150@gmail.com",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="123456",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
            'tc' => 'required',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response([
                'message' => 'Email already exists',
                'status' => 'failed'
            ], 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tc' => json_decode($request->tc),
        ]);

        return response([
            'message' => 'Register success',
            'status' => 'success'
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user and generate JWT token",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
        $user = User::where('email', $request->email)->first();
        if($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken($request->email)->plainTextToken;
            return response([
                'token' => $token,
                'message' => 'Login success',
                'status' => 'success',
            ], 200);
        }
        return response([
            'message' => 'the provided credentials are incorrect',
            'status' => 'failed'
        ], 401);

    }

}
