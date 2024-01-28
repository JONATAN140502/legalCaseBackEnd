<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Person;
use App\Models\User;
use App\Models\Lawyer;

class AbogadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datosAbogados = [
            ['16725844', 'Cubas', 'Mori', 'Manuel Fernando', '948417991', 'mcubasmo@unprg.edu.pe'],
            ['45616071', 'Santamaría', 'Chapoñan', 'Isvin', '998962921', 'isantamariac@unprg.edu.pe'],
            ['40640979', 'Fernández', 'Acha', 'Omar Simón', '948499363', 'ofernandezac@unprg.edu.pe'],
            ['16799993', 'Torres', 'Oballe', 'Juan Carlos', '948569277', 'jtorreso@unprg.edu.pe'],
            ['16794061', 'Rios', 'Lora', 'Christian Miguel', '991526970', 'crioslo@unprg.edu.pe'],
            ['47345175', 'Guevara', 'Acosta', 'Diana Carolina', '975943495', 'cguevaraac@unprg.edu.pe'],
            ['16797179', 'Coronado', 'Rioja', 'Antonio Lenin', '957945569', 'acoronador@unprg.edu.pe'],
            ['71415468', 'Delgado', 'Burga', 'Evelyn', '978172422', 'edelgadobu@unprg.edu.pe'],
        ];

        foreach ($datosAbogados as $datosAbogado) {
            $personaNatural = Person::create([
                'nat_dni' => $datosAbogado[0],
                'nat_apellido_paterno' => $datosAbogado[1],
                'nat_apellido_materno' => $datosAbogado[2],
                'nat_nombres' => $datosAbogado[3],
                'nat_telefono' => $datosAbogado[4],
                'nat_correo' => $datosAbogado[5],
                'per_condicion'=>'ABOGADO'
            ]);

            User::create([
                'name' => explode(' ', $datosAbogado[3])[0],
                'email' => $datosAbogado[5],
                'usu_rol' => 'ABOGADO',
                'per_id' => $personaNatural->per_id,
                'email_verified_at' => now(),
                'password' => Hash::make($datosAbogado[0]),
            ]);

            Lawyer::create([
                'abo_carga_laboral' => 0,
                'abo_disponibilidad' => 'LIBRE',
                'per_id' => $personaNatural->per_id,
            ]);
        }
    }
}
