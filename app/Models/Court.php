<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'co_id';
    protected $fillable = [
        'co_nombre',
        'judis_id',
        'co_isFvorite',
        'type_id'
    ];
    protected $dates = ['deleted_at'];

    public function proceeding()
    {
        return $this->hasMany(Proceeding::class, 'co_id', 'exp_juzgado');
    }

    public function judicialdistrict()
    {
        return $this->belongsTo(JudicialDistrict::class, 'judis_id');
    }
}
