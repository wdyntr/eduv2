<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    public $timestamps = false;
    protected $fillable = ['nama', 'jenjang'];
}