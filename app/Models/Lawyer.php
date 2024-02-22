<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lawyer extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'abo_id';
    protected $fillable = [
        'abo_carga_laboral',
        'abo_disponibilidad',
        'per_id'
    ];
    protected $dates = ['deleted_at'];

    
    public function persona()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }

    public function trades(){
        return $this->hasMany(Trade::class, 'tra_abo_id', 'abo_id');
    }

}
