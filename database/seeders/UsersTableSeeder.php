<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("users")->insert([
            'name' => 'Дмитрий',
            'email' => 'dimka@bdima.ru',
            'password' => Hash::make('123123'),
            'empLimit' => 100000,
            'unitLimit' => 100000,
            'graduateLimit' => 100000,
        ]);
    }
}
