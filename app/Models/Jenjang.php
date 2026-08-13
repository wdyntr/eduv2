<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenjang extends Model
{
    protected $table = 'jenjang';
    public $timestamps = false;
    protected $fillable = ['kode', 'nama'];
}