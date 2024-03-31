<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalDocument extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'legal_documents';
    protected $primaryKey = 'doc_id';
    protected $fillable = [
        'doc_nombre',
        'doc_tipo',
        'doc_desciprcion',
        'doc_ruta_archivo',
        'exp_id',
        'fecha',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    public function proceeding()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id', 'exp_id');
    }
}
