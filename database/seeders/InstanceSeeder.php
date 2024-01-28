<?php

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $currentTimestamp = Carbon::now();

        $instancias = [
            ['ins_nombre' => 'Juzgado de Paz Letrado'],
            ['ins_nombre' => 'Juzgado Especializado'],
            ['ins_nombre' => 'Juzgado Mixto'],
            ['ins_nombre' => 'Sala Superior']
        ];

        foreach ($instancias as $instancia) {
            $instancia['created_at'] = $currentTimestamp;
            $instancia['updated_at'] = $currentTimestamp;
        }

        Instance::insert($instancias);
    }
}
