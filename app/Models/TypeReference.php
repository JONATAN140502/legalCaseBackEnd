<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeReference extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'type_id';
    protected $fillable = [
        'type_name',
    ];
}
