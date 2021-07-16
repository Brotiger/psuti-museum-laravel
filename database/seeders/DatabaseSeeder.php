<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PagesTableSeeder::class,
            UsersTableSeeder::class,
            EmployeesTableSeeder::class,
            GraduatesTableSeeder::class,
            EventsTableSeeder::class,
            UnitsTableSeeder::class,
        ]);
    }
}
