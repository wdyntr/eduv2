<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PassagesImport;
use App\Imports\QuestionsImport;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:ods,xlsx,xls'
        ]);

        $file = $request->file('file');

        // 1. Import passages DULU
        Excel::import(new PassagesImport, $file);

        // Cek passages sudah masuk
        $count = \App\Models\Passage::count();
        if ($count === 0) {
            return redirect()->back()->with('error', 'Passages gagal diimport!');
        }

        // 2. Baru import questions
        Excel::import(new QuestionsImport, $file);

        return redirect()->back()->with('success', 'Import berhasil! ' . $count . ' passages masuk.');
    }
}