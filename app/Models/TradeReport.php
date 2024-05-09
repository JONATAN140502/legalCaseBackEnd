<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trade;
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
        'rep_pdf_informe'
    ];

    public function trade()
    {
        return $this->belongsTo(Trade::class, 'rep_tra_id', 'tra_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'rep_are_id', 'are_id');
    }
}
