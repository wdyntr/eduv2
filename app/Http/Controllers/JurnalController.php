<?php
namespace App\Http\Controllers;

use App\Models\Jurnal;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    public function index()
    {
        return view('jurnal', ['active_page' => 'jurnal']);
    }

    public function show(int $id)
    {
        // "Approved" sekarang ditentukan dari status jurnal_review milik
        // revisi terbaru, bukan kolom `status` langsung di tabel jurnal.
        $jurnal = Jurnal::with(['kategori', 'revisiTerbaru.reviewTerbaru'])
            ->whereHas('revisiTerbaru.reviewTerbaru', fn($q) => $q->where('status', 'approved'))
            ->findOrFail($id);

        return view('jurnal_detail', ['active_page' => 'jurnal', 'jurnal' => $jurnal]);
    }
}