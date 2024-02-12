<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trade extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'tra_id';
    protected $fillable = [
        'tra_number',
        'tra_number_ext',
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
    ];
}
