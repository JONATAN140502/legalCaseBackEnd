<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JudicialDistrict extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'judicial_districts';
    protected $primaryKey = 'judis_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'judis_nombre',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Define la relación con el modelo Expediente.
     */
    public function proceeding()
    {
        return $this->hasMany(Proceeding::class,  'judis_id', 'exp_dis_judicial');
    }

    public function instance()
    {
        return $this->hasMany(Instance::class, 'ins_id');
    }

    public function court()
    {
        return $this->hasMany(Court::class, 'co_id');
    }
}
