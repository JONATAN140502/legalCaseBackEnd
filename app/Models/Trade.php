<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Lawyer;
use App\Models\Assistant;
use App\Models\Area;
use App\Models\Observation;

class Trade extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'tra_id';
    protected $fillable = [
        'tra_number',
        'tra_name',
        'tra_number_ext',
        'tra_doc_recep',
        'tra_matter',
        'tra_arrival_date',
        'tra_state_mp',
        'tra_state_law',
        'tra_ubication',
        'tra_type_person',
        'tra_pdf',
        'tra_are_id',
        'tra_abo_id',
        'tra_ass_id',
        'tra_obs'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'tra_are_id', 'are_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'tra_abo_id', 'abo_id');
    }

    public function assistant()
    {
        return $this->belongsTo(Assistant::class, 'tra_ass_id', 'ass_id');
    }

    public function persons(){
        return $this->belongsToMany(Person::class, 'person_trades', 'pt_tra_id', 'pt_per_id');
    }

    public function observations(){
        return $this->hasMany(Observation::class, 'obs_tra_id', 'tra_id');
    }
}
