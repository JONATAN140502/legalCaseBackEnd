<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'obs_id';
    protected $fillable = [
        'obs_title',
        'obs_description',
        'obs_derivative',
        'obs_state',
        'obs_tra_id',
        'obs_answer'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


}
