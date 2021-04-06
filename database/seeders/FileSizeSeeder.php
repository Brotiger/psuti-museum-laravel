<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("file_sizes")->insert([
            'name' => 'photo',
            'size' => 400
        ]);

        DB::table("file_sizes")->insert([
            'name' => 'video',
            'size' => 1024
        ]);

        DB::table("file_sizes")->insert([
            'name' => 'file',
            'size' => 2048
        ]);
    }
}
