<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialty extends Model
{ use HasFactory, SoftDeletes;
    protected $table = 'specialties';
    protected $primaryKey = 'esp_id';
    protected $fillable = [
        'esp_nombre',
        'ins_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    // public function instance()
    // {
    //     return $this->belongsTo(Instance::class, 'ins_id', 'ins_id');
    // }
    public function expedientes()
    {
        return $this->hasMany(Expediente::class, 'esp_id', 'exp_especialidad');
    }
}
