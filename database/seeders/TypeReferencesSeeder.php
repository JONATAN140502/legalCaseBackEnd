<?php

namespace Database\Seeders;
use App\Models\TypeReference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeReferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datosTypes = [
            ['Expediente'],
            ['Oficio'],
            ['Carpeta fiscal'],
            ['Oficio multiple'],
            ['Oficio circular'],
            ['Caso arbitral'],
            ['Citacion'],
            ['Memorando'],
            ['Informe'],
            ['Informe multiple'],
            ['Solicitud']
        ];

        foreach ($datosTypes as $datosType) {
            $type = TypeReference::create([
                'type_name' => $datosType[0],
            ]);
        }
    }
}
