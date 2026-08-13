<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $table = 'jurnal';
    public $timestamps = false;
    protected $fillable = ['kategori_id', 'user_id'];

    public function kategori()
    {
        return $this->belongsTo(JurnalKategori::class, 'kategori_id');
    }

    public function penulisAkun()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revisi()
    {
        return $this->hasMany(JurnalRevisi::class, 'jurnal_id')->orderBy('versi_ke');
    }

    /** Revisi paling baru — ini yang biasa ditampilkan di listing/detail */
    public function revisiTerbaru()
    {
        return $this->hasOne(JurnalRevisi::class, 'jurnal_id')->latestOfMany('versi_ke');
    }
}