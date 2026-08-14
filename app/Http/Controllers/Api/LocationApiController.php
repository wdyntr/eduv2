<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KotaKabupaten;

class LocationApiController extends Controller
{
    public function kotaKabupaten()
    {
        $items = KotaKabupaten::orderBy('nama')
            ->get([
                'id',
                'nama',
            ]);

        return response()->json([
            'items' => $items,
        ]);
    }
}