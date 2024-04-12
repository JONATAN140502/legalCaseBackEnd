<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TypeReference;
use App\Models\Lawyer;
use App\Models\TradeReport;
use App\Models\Area;

class Trade extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'tra_id';
    protected $fillable = [
        'tra_number',
        'tra_name',
        'tra_exp_ext',
        'tra_doc_recep',
        'tra_matter',
        'tra_arrival_date',
        'tra_state_law',
        'tra_ubication',
        'tra_pdf',
        'tra_are_id',
        'tra_abo_id',
        'tra_type_id'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'tra_are_id', 'are_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'tra_abo_id', 'abo_id');
    }

    public function type_reference()
    {
        return $this->belongsTo(TypeReference::class, 'tra_type_id', 'type_id');
    }

    public function report()
    {
        return $this->hasOne(TradeReport::class, 'rep_tra_id', 'tra_id');
    }
}
