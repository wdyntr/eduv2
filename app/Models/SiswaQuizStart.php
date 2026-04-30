<?php
// app/Models/SiswaQuizStart.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaQuizStart extends Model
{
    protected $table    = 'siswa_quiz_starts';
    protected $fillable = ['user_id', 'session_id', 'started_at', 'deadline_at'];
    protected $casts    = [
        'started_at'  => 'datetime',
        'deadline_at' => 'datetime',
    ];
}
