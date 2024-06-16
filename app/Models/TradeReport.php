<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trade;
use App\Models\Proceeding;
use App\Models\Area;

class TradeReport extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'rep_id';
    protected $fillable = [
        'rep_informe',
        'rep_oficio',
        'rep_tra_id',
        'rep_are_id',
        'rep_anio',
        'rep_pdf_oficio',
        'rep_pdf_informe',
        'rep_exp_id',
        'rep_ext_informe',
        'rep_matter',
        'rep_arrival_date',
        'rep_abo_id'
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class, 'rep_tra_id', 'tra_id');
    }

    public function proceeding()
    {
        return $this->belongsTo(Proceeding::class, 'rep_exp_id', 'exp_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'rep_are_id', 'are_id');
    }
}
