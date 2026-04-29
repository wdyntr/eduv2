<?php
// app/Models/Passage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passage extends Model
{
    protected $fillable = ['subject', 'content'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}