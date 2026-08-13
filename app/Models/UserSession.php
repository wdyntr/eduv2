<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $table = 'user_sessions';
    protected $primaryKey = 'token';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = ['token', 'user_id', 'username', 'expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}