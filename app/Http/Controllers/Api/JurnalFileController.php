<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JurnalFileController extends Controller
{
    public function download(Request $request, int $id, string $type = 'jurnal')
    {
        $jurnal = Jurnal::with(['revisiTerbaru.reviewTerbaru'])->findOrFail($id);

        $revisi = $jurnal->revisiTerbaru;
        abort_unless($revisi, 404, 'File jurnal tidak tersedia.');

        $sourceField = $type === 'bukti' ? $revisi->file_bukti_plagiarisme : $revisi->file_jurnal;
        abort_unless($sourceField, 404, 'File tidak tersedia.');

        $filename = basename($sourceField);

        if (!Storage::disk('jurnal')->exists($filename)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $review = $revisi->reviewTerbaru;
        $isApproved = $review && $review->status === 'approved';

        if (!$isApproved) {
            $user = $request->user();
            abort_unless($user, 403, 'Anda harus login untuk mengakses file ini.');

            $isOwner = $user->id === $jurnal->user_id;
            $isReviewer = $user->can('jurnal.review') || $user->can('jurnal.lihat');
            abort_unless($isOwner || $isReviewer, 403, 'Anda tidak memiliki akses ke file ini.');
        }

        return Storage::disk('jurnal')->download($filename, $sourceField);
    }
}
