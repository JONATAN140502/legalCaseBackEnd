<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Person;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datosAdmins = [

            ['18189207', 'Palomino', 'Guerra', 'Carlos Andrés', '979945594', 'cpalominog@unprg.edu.pe'],
            ['77176517', 'Fernandez', 'Delgado', 'Cleiver Norberto', '966691495', 'cfdelgado@unprg.edu.pe'],
            ['74864514', 'Mayanga', 'Muñoz', 'Jonatan Arturo', '919614613', 'jmayangamu@unprg.edu.pe']
        ];

        foreach ($datosAdmins as $datosAdmin) {
            $personaNatural = Person::create([
                'nat_dni' => $datosAdmin[0],
                'nat_apellido_paterno' => $datosAdmin[1],
                'nat_apellido_materno' => $datosAdmin[2],
                'nat_nombres' => $datosAdmin[3],
                'nat_telefono' => $datosAdmin[4],
                'nat_correo' => $datosAdmin[5],
            ]);

            User::create([
                'name' => explode(' ', $datosAdmin[3])[0],
                'email' => $datosAdmin[5],
                'usu_rol' => 'ADMIN',
                'per_id' => $personaNatural->per_id,
                'email_verified_at' => now(),
                'password' => Hash::make($datosAdmin[0]),
            ]);
        }
    }
}
