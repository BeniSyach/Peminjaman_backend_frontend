<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10); // Default 10 data per halaman
            $user = $request->user(); // Ambil user yang sedang login
    
            $query = User::query();
    
            // Jika user bukan admin, hanya tampilkan user dengan role "customer"
            if ($user->role !== 'admin') {
                $query->where('role', 'customer');
            }
    
            $users = $query->paginate($perPage);
    
            return response()->json([
                'success' => true,
                'message' => 'List users',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail user',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'nik' => 'nullable|string|size:16|regex:/^[0-9]+$/',
                'nama' => 'nullable|string',
                'alamat' => 'nullable|string',
                'password' => 'nullable|string|min:6|confirmed',
                'role' => 'nullable|in:admin,petugas,customer',
                'no_hp' => 'nullable|string',
            ]);

            $user->update($request->only(['nik', 'nama', 'alamat', 'password', 'role', 'no_hp']));

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan atau gagal dihapus',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
