<?php

// app/Models/Passage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    protected $fillable = ['id', 'subject', 'content'];

    public $incrementing = false; // ← izinkan id manual dari Excel
}