<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PretensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $pretensiones = [
            ['pre_id' => '01', 'pre_nombre' => 'AFP - Pagos'],
            ['pre_id' => '02', 'pre_nombre' => 'Aplicación de la Ley 31542'],
            ['pre_id' => '03', 'pre_nombre' => 'Beneficios sociales'],
            ['pre_id' => '04', 'pre_nombre' => 'Bonificación por 25 y 30 años'],
            ['pre_id' => '05', 'pre_nombre' => 'CAFAE'],
            ['pre_id' => '06', 'pre_nombre' => 'FONAVI – 10%'],
            ['pre_id' => '07', 'pre_nombre' => 'Homologación'],
            ['pre_id' => '08', 'pre_nombre' => 'Impugnación de sanción administrativa'],
            ['pre_id' => '09', 'pre_nombre' => 'Impugnación de sanción docente'],
            ['pre_id' => '10', 'pre_nombre' => 'Inclusión a planillas'],
            ['pre_id' => '11', 'pre_nombre' => 'Indemnización'],
            ['pre_id' => '12', 'pre_nombre' => 'Inaplicación de Ley 31364'],
            ['pre_id' => '13', 'pre_nombre' => 'O.D.S.D'],
            ['pre_id' => '14', 'pre_nombre' => 'Quinquenios'],
            ['pre_id' => '15', 'pre_nombre' => 'Racionamiento'],
            ['pre_id' => '16', 'pre_nombre' => 'Reconocimiento de ascenso docente'],
            ['pre_id' => '17', 'pre_nombre' => 'Refrigerio y Movilidad'],
            ['pre_id' => '18', 'pre_nombre' => 'Reposición y otros'],
            ['pre_id' => '19', 'pre_nombre' => 'Subsidio por luto y fallecimiento'],
            ['pre_id' => '20', 'pre_nombre' => 'Otros'],
        ];

        $currentTimestamp = Carbon::now();
        foreach($pretensiones as &$pretension){
            // $pretension['pre_nombre'] = mb_strtoupper($pretension['pre_nombre']);
            $pretension['created_at'] = $currentTimestamp;
            $pretension['updated_at'] = $currentTimestamp;
        }
        
        DB::table('claims')->insert($pretensiones);
    }
}
