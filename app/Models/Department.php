<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'dep_id';
    protected $fillable = [
        'dep_nombre',
    ];
    protected $dates = ['deleted_at'];

    public function direccion(){
        return $this->hasMany(Address::class, 'dep_id');
    }
}
