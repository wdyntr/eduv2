<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';
    public $timestamps = false;
    protected $fillable = ['username', 'password', 'nama', 'role'];
    protected $hidden = ['password'];
}
