<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Trade extends Model
{
    protected $table = 'trades'; // Especifica el nombre de la tabla si es diferente a la convención 'trades'
    protected $primaryKey = 'id'; // Especifica la clave primaria si es diferente a 'id'
    public $timestamps = false; // Si no tienes columnas de timestamps created_at y updated_at

    protected $fillable = [
        'tra_number',
        'tra_number_ext',
        'tra_matter',
        'tra_arrival_date',
        'tra_format',
        'tra_state_mp',
        'tra_state_law',
        'tra_ubication',
        'tra_are_id',
        'tra_law_id',
        'tra_per_id',
    ];

    // Puedes agregar relaciones u otros métodos según tus necesidades
}