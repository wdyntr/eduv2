<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnalReview extends Model
{
    protected $table = 'jurnal_review';
    public $timestamps = false;
    protected $fillable = ['jurnal_revisi_id', 'status', 'catatan_admin', 'reviewed_by', 'reviewed_at'];

    public function revisi()
    {
        return $this->belongsTo(JurnalRevisi::class, 'jurnal_revisi_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}