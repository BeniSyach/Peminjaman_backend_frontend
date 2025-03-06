<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }

    // Register User
    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nik' => 'required|string|size:16|regex:/^[0-9]+$/|unique:users,nik',
                'nama' => 'required|string',
                'alamat' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|in:admin,petugas,customer',
                'no_hp' => 'required|string',
            ]);
    
            $user = User::create([
                'nik' => $validatedData['nik'],
                'nama' => $validatedData['nama'],
                'alamat' => $validatedData['alamat'],
                'password' => Hash::make($validatedData['password']),
                'role' => $validatedData['role'],
                'no_hp' => $validatedData['no_hp'],
            ]);

            
    
            return response()->json([
                'message' => 'User berhasil terdaftar',
                'user' => $user
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mendaftarkan user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // Login User
    public function login(Request $request)
    {
        try {
            $request->validate([
                'nik' => 'required|string|size:16|regex:/^[0-9]+$/',
                'password' => 'required|string',
            ]);
    
            $user = User::where('nik', $request->nik)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'NIK atau password salah'
                ], 401);
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;

            Auth::login($user);
    
            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user,
                'token' => $token
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat login',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function logout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthorized. Token tidak valid atau sudah kadaluarsa'
                ], 401);
            }

            $request->user()->tokens()->delete();

            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Logout berhasil'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthorized. Silakan login terlebih dahulu'
                ], 401);
            }

            return response()->json([
                'message' => 'Profile berhasil diambil',
                'user' => $request->user()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
