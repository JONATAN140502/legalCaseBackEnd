<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'dir_id';
    protected $fillable = [
        'dir_calle_av',
        'dis_id',
        'pro_id',
        'dep_id',
        'per_id'
    ];
    protected $dates = ['deleted_at'];
    public function district()
    {
        return $this->belongsTo(District::class, 'dis_id', 'dis_id');
    }

    public function provincia() {
        return $this->belongsTo(Province::class, ' pro_id');
    }

    public function  departamento(){
        return $this->belongsTo(Department::class, 'dep_id');
    }

    public function persona(){
        return $this->belongsTo(Person::class, 'per_id','per_id');
    }
}
