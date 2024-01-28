<?php

namespace Database\Seeders;

use App\Models\Proceeding;
use Database\Factories\ExpedienteFactory;
use Illuminate\Database\Seeder;

class ExpedientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\Person::factory()->count(50)->create();
        \App\Models\Proceeding::factory()->count(50)->create();
        \App\Models\Address::factory()->count(50)->create();
        \App\Models\Alert::factory()->count(25)->create();
        \App\Models\Audience::factory()->count(25)->create();
    }
}
