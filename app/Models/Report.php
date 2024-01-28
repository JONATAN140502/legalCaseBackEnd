<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'reports';
    protected $primaryKey = 'rep_id';
    protected $fillable = [
        'rep_fecha_generacion',
        'rep_tipo',
        'usu_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    
}
