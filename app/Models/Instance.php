<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Instance extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'instances';
    protected $primaryKey = 'ins_id';
    protected $fillable = [
        'ins_nombre',
        'judis_id',
        'type_id'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function judicialdistrict()
    {
        return $this->belongsTo(JudicialDistrict::class, 'ins_id', 'exp_instancia');
    }
}

