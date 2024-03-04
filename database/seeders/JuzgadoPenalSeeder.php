<?php

namespace Database\Seeders;

use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JuzgadoPenalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $juzgados = [
            ["co_nombre" => "INVEST. PREPARATORIA LAMBAYEQUE", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17],
            ["co_nombre" => "10° INVEST. PREPARATORIA LAMBAYEQUE", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17],
            ["co_nombre" => "PENAL UNIPERSONAL LAMBAYEQUE", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17],
            ["co_nombre" => "39° PENAL LIQUIDADOR - ANSE", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17],
            ["co_nombre" => "UNIPERSONAL - S. BELLAVISTA", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17],
            ["co_nombre" => "10° INV. PREP. ESP. DELITO DE CORRUPCIÓN DE FUNCIONARIOS - LAMBAYEQUE", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17],
            ["co_nombre" => "UNIPERSONAL LAMBAYEQUE", "co_isFavorite" => 0, "type_id" => 2, "judis_id" => 17]
        ];
    
        $currentTimestamp = Carbon::now();
        foreach ($juzgados as &$juzgado) {
            $juzgado['created_at'] = $currentTimestamp;
            $juzgado['updated_at'] = $currentTimestamp;
        }
    
        Court::insert($juzgados);
    }
    
}
