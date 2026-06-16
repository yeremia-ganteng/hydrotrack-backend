<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // ── Register ──────────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'daily_target'      => 2000, // default target
        ]);

        return response()->json(['message' => 'User berhasil dibuat!'], 201);
    }

    // ── Login ─────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah.'], 422);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'daily_target' => $user->daily_target ?? 2000,
            ],
        ]);
    }

    // ── Get Profile ───────────────────────────────────────────
    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'daily_target' => $user->daily_target ?? 2000,
                'created_at'   => $user->created_at->format('d M Y'),
            ],
        ]);
    }

    // ── Update Profile (name) ─────────────────────────────────
    public function updateProfile(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $user = $request->user();
        $user->update(['name' => $request->name]);
        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data'    => ['name' => $user->name],
        ]);
    }

    // ── Update Target Harian ──────────────────────────────────
    public function updateTarget(Request $request)
    {
        $request->validate([
            'daily_target' => 'required|integer|min:500|max:10000',
        ]);
        $user = $request->user();
        $user->update(['daily_target' => $request->daily_target]);
        return response()->json([
            'status'  => 'success',
            'message' => 'Target berhasil diperbarui',
            'data'    => ['daily_target' => $user->daily_target],
        ]);
    }

    // ── Logout ────────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

public function showLogin()
{
    // Gunakan Auth::check()
    if (Auth::check()) {
        return redirect('/');
    }
    
    return view('login');
}
}