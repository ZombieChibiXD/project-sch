<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdministratorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administator Agent',
            'username' => 'sysadmin',
            'level' => '1',
            'email' => 'sysadmin@zkabane.com',
            'password' => bcrypt('nimdasys'),
        ]);
    }
}
