<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Sekolah::where('is_active', 1);

        if ($request->search) $query->where('nama', 'like', "%{$request->search}%");
        if ($request->jenjang) $query->where('jenjang', $request->jenjang);

        $query->orderBy('nama', $request->get('sort', 'az') === 'za' ? 'desc' : 'asc');

        $items = $query->get(['id', 'nama', 'jenjang', 'kota_kabupaten', 'classroom_url']);
        return response()->json(['items' => $items, 'total' => $items->count()]);
    }
}