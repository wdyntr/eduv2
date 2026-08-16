<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ThumbnailProxyController extends Controller
{
    public function show(string $id)
    {
        $path = "thumbnail-cache/{$id}.jpg";

        if (!Storage::disk('public')->exists($path)) {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0',
            ])->get("https://drive.google.com/thumbnail?id={$id}&sz=w1000");

            if (!$response->ok() || !str_starts_with($response->header('Content-Type'), 'image')) {
                abort(404, 'Gambar tidak ditemukan atau file Drive belum di-share publik.');
            }

            Storage::disk('public')->put($path, $response->body());
        }

        return response(Storage::disk('public')->get($path))
            ->header('Content-Type', 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
