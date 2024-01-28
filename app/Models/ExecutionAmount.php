<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExecutionAmount extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'execution_amounts';
    protected $primaryKey = 'ex_id';
    protected $fillable = [
        'ex_ejecucion_1',
        'ex_ejecucion_2',
        'ex_interes_1',
        'ex_interes_2',
        'ex_costos',
        'exp_id',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
   
} 
