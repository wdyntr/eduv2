<?php
namespace App\Http\Controllers;

use App\Models\Sekolah;

class SekolahController extends Controller
{
    public function show(int $id)
    {
        $sekolah = Sekolah::where('is_active', 1)->findOrFail($id);
        return view('sekolah_detail', ['active_page' => 'classroom', 'sekolah' => $sekolah]);
    }
}