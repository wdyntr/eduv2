<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ptn extends Model
{
    protected $table = 'ptn';

    protected $fillable = [
        'logo',
        'ptn',
    ];

    public function prodi(): HasMany
    {
        return $this->hasMany(PtnProdi::class, 'ptn_id');
    }
}
