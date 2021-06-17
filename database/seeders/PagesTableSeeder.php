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
            "Филиалы",
            "СРТТЦ",
            "Проректоры, деканы, зав. кафедры",
            "Научная деятельность",
            "Материально техническая база",
            "Академия АТИ",
            'Ассоциация "Телеинфо"',
        ];

        foreach($pages as $page){
            DB::table("pages")->insert([
                'title' => $page,
            ]);
        }
    }
}
