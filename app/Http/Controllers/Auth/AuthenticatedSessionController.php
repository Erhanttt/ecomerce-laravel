<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * LOGIN API për React + Sanctum
     */
    public function store(LoginRequest $request)
    {
        // ✅ Verifikon kredencialet (email + password)
        $request->authenticate();

        // ✅ Merr përdoruesin
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Përdoruesi nuk u gjet'], 404);
        }

        // ✅ Krijon token për API
        $token = $user->createToken('auth_token')->plainTextToken;

        // ✅ Kthen përgjigje JSON
        return response()->json([
            'message' => 'Login i suksesshëm!',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * LOGOUT API për React + Sanctum
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logout u krye me sukses']);
    }
}
