<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::orderBy('nama');
        if ($request->jenjang) {
            $query->where('jenjang', $request->jenjang);
        }
        return response()->json(['items' => $query->get()]);
    }
}