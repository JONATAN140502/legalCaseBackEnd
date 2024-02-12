<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Person;
use App\Models\User;
use App\Models\Assistant;
use Illuminate\Support\Facades\Hash;

class AssistantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datosAsistentes = [
            ['19000063', 'Gomez', 'Perez', 'Ana Maria', '987654321', 'aperez@unprg.edu.pe'],
            ['21547896', 'Rodriguez', 'Lopez', 'Carlos Alberto', '951234567', 'crodriguez@unprg.edu.pe'],
            ['12345678', 'Martinez', 'Gonzalez', 'Laura Isabel', '910876543', 'lmartinez@unprg.edu.pe'],
            ['98765432', 'Lopez', 'Torres', 'Pedro Jose', '975318642', 'plopez@unprg.edu.pe'],
        ];

        foreach ($datosAsistentes as $datosAsistente) {
            $personaNatural = Person::create([
                'nat_dni' => $datosAsistente[0],
                'nat_apellido_paterno' => $datosAsistente[1],
                'nat_apellido_materno' => $datosAsistente[2],
                'nat_nombres' => $datosAsistente[3],
                'nat_telefono' => $datosAsistente[4],
                'nat_correo' => $datosAsistente[5],
                'per_condicion'=>'ASISTENTE'
            ]);

            User::create([
                'name' => explode(' ', $datosAsistente[3])[0],
                'email' => $datosAsistente[5],
                'usu_rol' => 'ASISTENTE',
                'per_id' => $personaNatural->per_id,
                'email_verified_at' => now(),
                'password' => Hash::make($datosAsistente[0]),
            ]);

            Assistant::create([
                'ass_carga_laboral' => 0,
                'ass_disponibilidad' => 'LIBRE',
                'per_id' => $personaNatural->per_id,
            ]);
        }
    }
}
