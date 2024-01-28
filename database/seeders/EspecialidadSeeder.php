<?php

namespace Database\Seeders;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $currentTimestamp = Carbon::now();

        $especialidades = [
            ['esp_nombre' => 'Civil'],
            ['esp_nombre' => 'Familia Civil'],
            ['esp_nombre' => 'Familia Tutelar'],
            ['esp_nombre' => 'Laboral'],
            ['esp_nombre' => 'Comercial'],
            ['esp_nombre' => 'Derecho Constitucional'],
            ['esp_nombre' => 'Juzgado de Paz Letrado']
        ];

        

        foreach ($especialidades as $especialidad) {
           $especialidad['created_at'] = $currentTimestamp;
           $especialidad['updated_at'] = $currentTimestamp;
        }

        Specialty::insert($especialidades);
    }
}
