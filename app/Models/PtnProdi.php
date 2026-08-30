<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtnProdi extends Model
{
    protected $table = 'ptn_prodi';

    protected $fillable = [
        'kode',
        'ptn_id',
        'prodi',
        'jenjang',
        'grade',
        'persen',
    ];

    public function ptn(): BelongsTo
    {
        return $this->belongsTo(Ptn::class, 'ptn_id');
    }
}
