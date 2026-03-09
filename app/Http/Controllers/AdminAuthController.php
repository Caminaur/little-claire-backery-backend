<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\LoginAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(LoginAdminRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        $request->session()->regenerate();

        return response()->noContent();
    }

    public function me(Request $request)
    {
        $admin = $request->user();
        return response()->json(['id' => $admin->id, 'email' => $admin->email]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
