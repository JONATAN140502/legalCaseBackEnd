<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProceedingTypes extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'proceeding_types';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name'
    ];
    protected $dates = ['deleted_at'];
    public function expediente()
    {
        return $this->hasMany(Proceeding::class, 'exp_id');
    }
}
