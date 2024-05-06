<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeProceeding extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'offices_proceeding';

    protected $fillable = [
        'expediente_id',
        'n_correlativo',
        'asunto',
        'fecha_envio',
        'destinatario',
    ];

    // Relación con el expediente
    public function proceeding()
    {
        return $this->belongsTo(Proceeding::class, 'expediente_id', 'exp_id');
    }
}
