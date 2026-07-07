<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalKategori extends Model
{
    protected $table = 'jurnal_kategori';
    public $timestamps = false;
    protected $fillable = ['nama'];
}
