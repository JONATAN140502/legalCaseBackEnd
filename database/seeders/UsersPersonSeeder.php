<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UsersPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserta la primera persona natural manualmente
        DB::table('people_naturals')->insert([
            'nat_dni' => 77176517,
            'nat_nombres' => 'Cleiver',
            'nat_apellido_paterno' => 'Fernandez',
            'nat_apellido_materno' => 'Delgado',
            'nat_telefono' => '+51 980404074', // Asegúrate de incluir el prefijo internacional para Perú
            'nat_correo' => 'cleiverfd@gmail.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta un registro en la tabla users relacionado con la primera persona natural
        DB::table('users')->insert([
            'name' => 'Cleiver',
            'email' => 'cleiverfd@gmail.com',
            'usu_rol' => 'ADMIN', // Cambia esto si el rol es diferente
            'per_id' => 1, // El ID de la primera persona natural manualmente ingresada
            'password' => Hash::make('77176517'), // Usando el DNI como contraseña
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta la segunda persona natural manualmente
        DB::table('people_naturals')->insert([
            'nat_dni' => 74864514,
            'nat_nombres' => 'Jonatan',
            'nat_apellido_paterno' => 'Mayanga',
            'nat_apellido_materno' => 'Muñoz',
            'nat_telefono' => '+51 956342789', // Asegúrate de incluir el prefijo internacional para Perú
            'nat_correo' => 'jonatanmayanga@gmail.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta un registro en la tabla users relacionado con la segunda persona natural
        DB::table('users')->insert([
            'name' => 'Jonatan',
            'email' => 'jonatanmayanga@gmail.com',
            'usu_rol' => 'ADMIN', // Cambia esto si el rol es diferente
            'per_id' => 2, // El ID de la segunda persona natural manualmente ingresada
            'password' => Hash::make('74864514'), // Usando el DNI como contraseña
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

         // Inserta la tercera persona natural manualmente
        DB::table('people_naturals')->insert([
            'nat_dni' => 18189207,
            'nat_nombres' => 'Carlos Andres',
            'nat_apellido_paterno' => 'Palomino',
            'nat_apellido_materno' => 'Guerra',
            'nat_telefono' => '+51 979945594', 
            'nat_correo' => 'cpalominog@unprg.edu.pe',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta un registro en la tabla users relacionado con la segunda persona natural
        DB::table('users')->insert([
            'name' => 'Carlos',
            'email' => ' cpalominog@unprg.edu.pe',
            'usu_rol' => 'ADMIN', // Cambia esto si el rol es diferente
            'per_id' => 3, // El ID de la segunda persona natural manualmente ingresada
            'password' => Hash::make('18189207'), // Usando el DNI como contraseña
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
