<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assistant extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'ass_id';
    protected $fillable = [
        'ass_carga_laboral',
        'ass_disponibilidad',
        'per_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function persona()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }
}
