<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah';
    public $timestamps = false;
    protected $fillable = ['nama', 'jenjang', 'kota_kabupaten', 'classroom_url', 'is_active'];
}