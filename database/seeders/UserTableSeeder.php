<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create(

            [
                'user_type' => 'ADMIN',
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('admin123'),
            ]

        );

        User::create(

            [
                'user_type' => 'CUSTOMER',
                'name' => 'Customer User 1',
                'email' => 'customer1@test.com',
                'password' => Hash::make('admin123'),
            ]

        );

        User::create(

            [
                'user_type' => 'CUSTOMER',
                'name' => 'Customer User 2',
                'email' => 'customer2@test.com',
                'password' => Hash::make('admin123'),
            ]

        );
    }
}
