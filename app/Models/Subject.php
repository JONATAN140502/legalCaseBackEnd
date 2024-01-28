<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'mat_id';
    protected $fillable = [
        'mat_nombre',
    ];
    protected $dates = ['deleted_at'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'mat_id', 'exp_materia');
    }
}

