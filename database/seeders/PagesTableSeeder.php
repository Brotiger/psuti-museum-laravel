<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pages = [
            "Филиалы" => "branches",
            "СРТТЦ" => "SRTTTS",
            "Научная деятельность" => "ScientificActivity",
            "Материально техническая база" => "MaterialAndTechnicalBase",
            "Академия АТИ" => "ATIAcademy",
            'Ассоциация "Телеинфо"' => "Teleinfo",
            "Филиал в городе Оренбург" => "branchOrenburg",
            "Филиал в городе Казань" => "branchKazan",
            "Филиал в городе Ставрополь" => "branchStavropol",
        ];

        foreach($pages as $name => $alias){
            DB::table("pages")->insert([
                'title' => $name,
                'alias' => $alias
            ]);
        }
    }
}
