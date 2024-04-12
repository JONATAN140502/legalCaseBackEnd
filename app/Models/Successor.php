<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Successor extends Model
{
    use HasFactory;
    protected $table = 'successors'; // Specify the table name if it's different from the plural of the model name

    protected $fillable = [
        'fallecido_id',
        'sucesor_id'
    ];

    public function fallecido()
    {
        return $this->belongsTo(Person::class, 'fallecido_id', 'per_id');
    }

    public function sucesor()
    {
        return $this->belongsTo(Person::class, 'sucesor_id', 'per_id');
    }
}
