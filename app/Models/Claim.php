<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Claim extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'pre_id';
    protected $fillable = [
        'pre_nombre'
    ];
    protected $dates = ['deleted_at'];

    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'pre_id', 'exp_pretencion');
    }
}
