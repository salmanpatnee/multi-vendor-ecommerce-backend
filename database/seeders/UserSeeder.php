<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@ecommerce.com',
                'password' => '123456',
                'role' => 'Administrator',
            ], 
            [
                'name' => 'Vendor',
                'username' => 'vendor',
                'email' => 'vendor@ecommerce.com',
                'password' => '123456',
                'role' => 'Vendor',
            ], 
            [
                'name' => 'Customer',
                'username' => 'customer',
                'email' => 'customer@ecommerce.com',
                'password' => '123456',
                'role' => 'Customer',
            ]
        ];

        foreach($users as $user){
            User::create([
                'name' => $user['name'],
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => $user['password'],
            ])->assignRole($user['role']);

        }
    }
}
