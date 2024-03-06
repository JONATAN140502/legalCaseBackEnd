<?php

namespace Database\Seeders;

use App\Models\ProceedingTypes;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProceedingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $currenTimestamp = Carbon::now();

        $proceedingTypes = [
            ['name' => 'Civil / Laboral', 'state' => '1'],
            ['name' => 'Penal',  'state' => '1'],
            ['name' => 'Arbitral', 'state' => '1'],
            ['name' => 'Indecopí', 'state' => '1']
        ];

        foreach ($proceedingTypes as $proceeding){
            $proceeding['created_at'] = $currenTimestamp;
            $proceeding['updated_at'] = $currenTimestamp;
        }

        ProceedingTypes::insert($proceedingTypes);
    }
}
