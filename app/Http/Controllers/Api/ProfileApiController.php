<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileApiController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'nama' => $user->nama,
            'roles' => $user->getRoleNames(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'password_lama' => ['nullable', 'string'],
            'password_baru' => ['nullable', 'string', 'min:6'],
        ]);

        $update = [
            'nama' => trim($data['nama']),
        ];

        if (!empty($data['password_baru'])) {
            if (empty($data['password_lama'])) {
                return response()->json([
                    'message' => 'Password lama wajib diisi.'
                ], 422);
            }

            if (!Hash::check($data['password_lama'], $user->password)) {
                return response()->json([
                    'message' => 'Password lama salah.'
                ], 422);
            }

            $update['password'] = Hash::make($data['password_baru']);
        }

        $user->update($update);

        return response()->json([
            'ok' => true,
            'message' => 'Profil berhasil diperbarui.',
        ]);
    }
}