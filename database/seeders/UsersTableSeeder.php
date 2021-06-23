<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Дмитрий',
            'email' => 'dimka@bdima.ru',
            'password' => Hash::make('123123'),
        ]);

        $user->limits()->create();
        $user->rights()->create();

        $user = User::create([
            'name' => 'Дмитрий2',
            'email' => 'dimka@bdima2.ru',
            'password' => Hash::make('123123'),
        ]);

        $user->limits()->create();
        $user->rights()->create();
    }
}
