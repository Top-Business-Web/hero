<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'abdullah',
                'email' => 'abdullah.driver@gmail.com',
                'phone' => '010454552655',
                'img' => 'assets/uploads/driver.jpeg',
                'birth' => '2023-10-11',
                'password' => Hash::make('123456'),
                'type' => 'driver',
                'status' => '0',
            ],
            [
                'name' => 'eldapour',
                'email' => 'eldapour.driver@gmail.com',
                'phone' => '0105456452',
                'img' => 'assets/uploads/driver.jpeg',
                'birth' => '2023-10-11',
                'password' => Hash::make('123456'),
                'type' => 'driver',
                'status' => '0',
            ],
            [
                'name' => 'eslam',
                'email' => 'eslam.driver@gmail.com',
                'phone' => '010458666664',
                'img' => 'assets/uploads/driver.jpeg',
                'birth' => '2023-10-11',
                'password' => Hash::make('123456'),
                'type' => 'driver',
                'status' => '0',
            ],
            [
                'name' => 'ahmed',
                'email' => 'ahmed.driver@gmail.com',
                'phone' => '01054564577',
                'img' => 'assets/uploads/driver.jpeg',
                'birth' => '2023-10-11',
                'password' => Hash::make('123456'),
                'type' => 'driver',
                'status' => '0',
            ],
        ];
        DB::table('users')->insert($data);
    }
}
