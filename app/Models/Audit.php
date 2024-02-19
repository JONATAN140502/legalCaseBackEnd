<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Audit extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'id';
    protected $fillable = [
        'accion',
        'model',
        'model_id',
        'user_id',
    ];
    protected $dates = [ 'au_fecha','deleted_at'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
