<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Procesal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'procesals';
    protected $primaryKey = 'proc_id';
    protected $fillable = [
        'per_id',
        'exp_id',
        'tipo_procesal',
        'tipo_persona'
    ];
    protected $dates = ['deleted_at'];

    public function persona()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }
    public function expediente()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id');
    }
}
