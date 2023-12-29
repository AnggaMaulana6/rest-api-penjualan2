<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
// use Dotenv\Exception\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if(! $user || ! Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'username' => ['username or password is failded']
            ]);
        }

        return $user->createToken('user token')->plainTextToken;
    }
    public function loginCustomer(Request $request) {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = Customer::where('username', $request->username)->first();

        if(! $user || ! Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'username' => ['username or password is failded']
            ]);
        }

        return $user->createToken('customer token')->plainTextToken;
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'username' => 'required|min:3|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
        ]);

        return response()->json(['user' => $user, 'message' => 'User registered successfully'], 201);
    }
    public function registerCustomer(Request $request) {
        $request->validate([
            'name' => 'required',
            'username' => 'required|min:3|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
        ]);

        $user = Customer::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),     
                'phone' => $request->phone,
            ]);

        return response()->json(['user' => $user, 'message' => 'User registered successfully'], 201);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Success Logout'], 201);

    }
    public function me(Request $request) {
        return response()->json(Auth::user());
    }
}
