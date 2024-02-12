<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Person;
use App\Models\User;


class SecretariaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        $datosSecretarias = [
            ['00123100', 'Alvarez', 'Flores', 'Celeste', '940007991', 'clores@unprg.edu.pe'],
            ['45613331', 'Perez', 'Huaman', 'Leydi', '998962111', 'leperez@unprg.edu.pe'],
        ];

        foreach ($datosSecretarias as $datosSecretaria) {
            $personaNatural = Person::create([
                'nat_dni' => $datosSecretaria[0],
                'nat_apellido_paterno' => $datosSecretaria[1],
                'nat_apellido_materno' => $datosSecretaria[2],
                'nat_nombres' => $datosSecretaria[3],
                'nat_telefono' => $datosSecretaria[4],
                'nat_correo' => $datosSecretaria[5],
            ]);

            User::create([
                'name' => explode(' ', $datosSecretaria[3])[0],
                'email' => $datosSecretaria[5],
                'usu_rol' => 'SECRETARIA',
                'per_id' => $personaNatural->per_id,
                'email_verified_at' => now(),
                'password' => Hash::make($datosSecretaria[0]),
            ]);

            
        }
    }
}
