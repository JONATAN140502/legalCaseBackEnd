<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable

{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'usu_rol',
        'per_id',
        'password',
    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function personaNatural()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }
    public function abogado()
    {
        return $this->hasMany(Lawyer::class, 'per_id', 'per_id');
    }

    public function assistant()
    {
        return $this->hasOne(Assistant::class, 'per_id', 'per_id');
    }

    //v1
    public function person()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }
}
