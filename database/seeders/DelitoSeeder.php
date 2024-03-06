<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DelitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $delitos = [
            ['pre_nombre' => 'Falsificación De Documentos', 'type_id' => 2],
            ['pre_nombre' => 'Apropiación Ilícita', 'type_id' => 2],
            ['pre_nombre' => 'Colusión', 'type_id' => 2],
            ['pre_nombre' => 'Usurpación', 'type_id' => 2],
            ['pre_nombre' => 'Peculado', 'type_id' => 2],
            ['pre_nombre' => 'Cohecho', 'type_id' => 2],
            ['pre_nombre' => 'Falsa Declaración En Proceso Administrativo', 'type_id' => 2],
            ['pre_nombre' => 'Abuso De Autoridad', 'type_id' => 2],
            ['pre_nombre' => 'Difamación', 'type_id' => 2],
            ['pre_nombre' => 'Falsedad Genérica', 'type_id' => 2],
            ['pre_nombre' => 'Falsedad Ideológica', 'type_id' => 2],
            ['pre_nombre' => 'Hurto', 'type_id' => 2],
            ['pre_nombre' => 'Nombramiento O Aceptación Indebida Para Cargo Público', 'type_id' => 2],
            ['pre_nombre' => 'Contra La Fe Pública', 'type_id' => 2],
            ['pre_nombre' => 'Delitos De Contaminación', 'type_id' => 2],
            ['pre_nombre' => 'Desobediencia Y Resistencia A La Autoridad', 'type_id' => 2],
            ['pre_nombre' => 'Plagio', 'type_id' => 2],
            ['pre_nombre' => 'Encubrimiento Real', 'type_id' => 2],
            ['pre_nombre' => 'Estafa', 'type_id' => 2],
            ['pre_nombre' => 'Contra Datos Y Sistemas Informáticos', 'type_id' => 2],
            ['pre_nombre' => 'Omisión O Retardo De Actos Funcionales', 'type_id' => 2],
            ['pre_nombre' => 'Incumplimiento De Deberes', 'type_id' => 2],
            ['pre_nombre' => 'Lavados De Activos', 'type_id' => 2],
            ['pre_nombre' => 'Cobro Indebido', 'type_id' => 2],
            ['pre_nombre' => 'Negociación Incompatible', 'type_id' => 2],
            ['pre_nombre' => 'Asociación Ilícita Para Delinquir', 'type_id' => 2]
        ];
    
        $currentTimestamp = Carbon::now();
        foreach ($delitos as &$delito) {
            $delito['created_at'] = $currentTimestamp;
            $delito['updated_at'] = $currentTimestamp;
        }
    
        DB::table('claims')->insert($delitos);
    }
    
}
