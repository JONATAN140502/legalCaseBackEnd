<?php

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class FiscaliaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $instancias = [
            ['ins_nombre' => 'Primera Fiscalía Provincial Penal Corporativa', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Segunda Fiscalía Provincial Penal Corporativa', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Fiscalía Provincial Especializada en Delitos de Corrupción de Funcionarios', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Fiscalía Especializada contra la Criminalidad Organizada Corporativa', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Especializada en Delitos de Corrupción de Funcionarios', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Fiscalía Provincial Penal de Condorcanqui', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Primer Despacho de Investigación', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Segunda Fiscalía Provincial Corporativa', 'type_id' => 2, 'judis_id' => 17],
            ['ins_nombre' => 'Tercer Despacho de Investigación', 'type_id' => 2, 'judis_id' => 17]
        ];
    
        $currentTimestamp = Carbon::now();
        foreach ($instancias as &$instancia) {
            $instancia['created_at'] = $currentTimestamp;
            $instancia['updated_at'] = $currentTimestamp;
        }
    
        Instance::insert($instancias);
    }
    
}
