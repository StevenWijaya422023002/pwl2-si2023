<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Mendapatkan semua user
    public function index()
    {
        return User::all();
    }

    // Mendapatkan user berdasarkan id
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        return $user;
    }

    // Menyimpan user baru
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8'
            ]);
    
            // Hash password
            $validatedData['password'] = bcrypt($validatedData['password']);
    
            // Buat user baru
            $user = User::create($validatedData);
    
            return response()->json([
                'success' => true,
                'data' => $user
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error dari validasi
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // Error lain yang tidak terduga
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // Memperbarui user berdasarkan id
    public function update(Request $request, $id)
    {
        try {
            // Cari user berdasarkan ID
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
    
            // Validasi input
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8'
            ]);
    
            // Hash password jika ada input password baru
            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            }
    
            // Update user
            $user->update($validatedData);
    
            return response()->json($user, 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // Tangani error lainnya
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data pengguna',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // Menghapus user berdasarkan id
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();
        return response()->json(null, 204);
    }
}
