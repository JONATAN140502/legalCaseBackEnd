<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datosAreas = [
            ['Secretaria General', 'sgeneral@gmail.com'],
            ['Recursos Humanos', 'rrhh@gmail.com'],
            ['FICSA', 'ficsa@gmail.com'],
            ['OPA', 'opa@gmail.com'],
            ['Asesoria Juridica', 'ajuridica@gmail.com'],
        ];

        foreach ($datosAreas as $datosArea) {
            $area = Area::create([
                'are_name' => $datosArea[0],
                'are_email' => $datosArea[1],
            ]);
        }

    }
}
