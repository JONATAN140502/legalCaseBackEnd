<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'provinces';
    protected $primaryKey = 'pro_id';
    protected $fillable = [
        'pro_nombre',
        'dep_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    public function departament()
    {
        return $this->belongsTo(Department::class, 'dep_id', 'dep_id');
    }

    public function direccion(){
        return $this->hasMany(Address::class, 'pro_id');
    }
}
