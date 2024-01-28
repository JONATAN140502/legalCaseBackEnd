<?php

namespace Database\Seeders;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTimestamp = Carbon::now();

        $departments = [
            ['dep_id' => '01', 'dep_nombre' => 'Amazonas'],
            ['dep_id' => '02', 'dep_nombre' => 'Áncash'],
            ['dep_id' => '03', 'dep_nombre' => 'Apurímac'],
            ['dep_id' => '04', 'dep_nombre' => 'Arequipa'],
            ['dep_id' => '05', 'dep_nombre' => 'Ayacucho'],
            ['dep_id' => '06', 'dep_nombre' => 'Cajamarca'],
            ['dep_id' => '07', 'dep_nombre' => 'Callao'],
            ['dep_id' => '08', 'dep_nombre' => 'Cusco'],
            ['dep_id' => '09', 'dep_nombre' => 'Huancavelica'],
            ['dep_id' => '10', 'dep_nombre' => 'Huánuco'],
            ['dep_id' => '11', 'dep_nombre' => 'Ica'],
            ['dep_id' => '12', 'dep_nombre' => 'Junín'],
            ['dep_id' => '13', 'dep_nombre' => 'La Libertad'],
            ['dep_id' => '14', 'dep_nombre' => 'Lambayeque'],
            ['dep_id' => '15', 'dep_nombre' => 'Lima'],
            ['dep_id' => '16', 'dep_nombre' => 'Loreto'],
            ['dep_id' => '17', 'dep_nombre' => 'Madre de Dios'],
            ['dep_id' => '18', 'dep_nombre' => 'Moquegua'],
            ['dep_id' => '19', 'dep_nombre' => 'Pasco'],
            ['dep_id' => '20', 'dep_nombre' => 'Piura'],
            ['dep_id' => '21', 'dep_nombre' => 'Puno'],
            ['dep_id' => '22', 'dep_nombre' => 'San Martín'],
            ['dep_id' => '23', 'dep_nombre' => 'Tacna'],
            ['dep_id' => '24', 'dep_nombre' => 'Tumbes'],
            ['dep_id' => '25', 'dep_nombre' => 'Ucayali'],
        ];

        foreach ($departments as &$department) {
            $department['created_at'] = $currentTimestamp;
            $department['updated_at'] = $currentTimestamp;
        }

        Department::insert($departments);
    }
}
