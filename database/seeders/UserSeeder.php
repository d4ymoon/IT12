<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrator')->first();

        if ($adminRole) {
            User::create([
                'f_name'     => 'Admin',
                'l_name'     => 'User',
                'username'   => 'admin',
                'email'      => 'admin@atinhardware.com',
                'role_id'    => $adminRole->id, 
                'is_active'  => true,
                'password'   => Hash::make('admin1234'), 
            ]);
        }
    }
}
