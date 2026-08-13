<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $table = 'users';
    public $timestamps = false;
    protected $fillable = ['username', 'password', 'nama', 'sekolah_id'];
    protected $hidden = ['password'];

    protected string $guard_name = 'web';

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function sessions()
    {
        return $this->hasMany(UserSession::class, 'user_id');
    }
}