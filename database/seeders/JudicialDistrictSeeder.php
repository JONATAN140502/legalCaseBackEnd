<?php

namespace Database\Seeders;

use App\Models\JudicialDistrict;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JudicialDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $currentTimestamp = Carbon::now();

        $distritos = [
            ['judis_nombre' => 'Amazonas'],
            ['judis_nombre' => 'Ancash'],
            ['judis_nombre' => 'Apurimac'],
            ['judis_nombre' => 'Arequipa'],
            ['judis_nombre' => 'Ayacucho'],
            ['judis_nombre' => 'Cajamarca'],
            ['judis_nombre' => 'Callao'],
            ['judis_nombre' => 'Cañete'],
            ['judis_nombre' => 'Cusco'],
            ['judis_nombre' => 'Del Santa'],
            ['judis_nombre' => 'Huancavelica'],
            ['judis_nombre' => 'Huanuco'],
            ['judis_nombre' => 'Huaura'],
            ['judis_nombre' => 'Ica'],
            ['judis_nombre' => 'Junin'],
            ['judis_nombre' => 'La Libertad'],
            ['judis_nombre' => 'Lambayeque'],
            ['judis_nombre' => 'Lima'],
            ['judis_nombre' => 'Lima Este'],
            ['judis_nombre' => 'Lima Norte'],
            ['judis_nombre' => 'Lima Sur'],
            ['judis_nombre' => 'Loreto'],
            ['judis_nombre' => 'Madre de Dios'],
            ['judis_nombre' => 'Moquegua'],
            ['judis_nombre' => 'Pasco'],
            ['judis_nombre' => 'Piura'],
            ['judis_nombre' => 'Puno'],
            ['judis_nombre' => 'San Martin'],
            ['judis_nombre' => 'Selva Central'],
            ['judis_nombre' => 'Sullana'],
            ['judis_nombre' => 'Tacna'],
            ['judis_nombre' => 'Tumbes'],
            ['judis_nombre' => 'Ucayali'],
            ['judis_nombre' => 'Ventanilla - Lima Noroeste'],
        ];

        foreach ($distritos as $distrito) {
            $distrito['created_at'] = $currentTimestamp;
            $distrito['updated_at'] = $currentTimestamp;
        }

        JudicialDistrict::insert($distritos);
    }
}
