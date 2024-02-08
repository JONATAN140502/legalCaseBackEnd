<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'are_id';
    protected $fillable = [
        'are_name',
        'are_email'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
