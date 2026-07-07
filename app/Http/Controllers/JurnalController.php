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
        $jurnal = Jurnal::where('status', 'approved')->findOrFail($id);
        return view('jurnal_detail', ['active_page' => 'jurnal', 'jurnal' => $jurnal]);
    }
}
