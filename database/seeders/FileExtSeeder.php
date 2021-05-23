<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FileExtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("file_exts")->insert([
            'name' => 'photo',
            'ext' => implode(', ', ['jpg','png','svg'])
        ]);

        DB::table("file_exts")->insert([
            'name' => 'video',
            'ext' => implode(', ', ['mp4','avi'])
        ]);

        DB::table("file_exts")->insert([
            'name' => 'file',
            'ext' => implode(', ', ['jpg','png','svg'])
        ]);
    }
}
