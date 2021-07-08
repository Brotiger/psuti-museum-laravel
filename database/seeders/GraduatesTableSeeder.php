<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Graduate;

class GraduatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Graduate::factory()->count(60)->create();
    }
}
