<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KotaKabupaten extends Model
{
    protected $table = 'kota_kabupaten';
    public $timestamps = false;
    protected $fillable = ['nama'];
}