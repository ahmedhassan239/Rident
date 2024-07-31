<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function index()
    {
        $users = User::select('id', 'name', 'email', 'created_at')->get();
        return response()->json(["data" => $users]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Successfully Deleted'], 200);
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'nullable',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'password' => bcrypt($validated['password']),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        if ($this->validateLogin($request)->fails()) {
            return response()->json($this->validateLogin($request)->errors(), 422);
        } else {
            $token = Auth::guard('api')->attempt($this->loginCredentials($request));

            if (!$token)
                return response()->json(['error' => 'Invalid User Or Password'], 401);

            return $this->respondWithToken($token);
        }
    }
    protected function validateLogin($request): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($this->loginCredentials($request), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    }

    // protected function respondWithToken($token): \Illuminate\Http\JsonResponse
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //     ]);
    // }
    protected function respondWithToken($token): \Illuminate\Http\JsonResponse
    {
        $user = Auth::guard('api')->user();

        return response()->json([
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                // Add any other user attributes you wish to share
            ]
        ]);
    }

    protected function loginCredentials(Request $request): array
    {
        return $request->only('email', 'password');
    }
}
