<?php

namespace Modules\Users\Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'akashkazi012@gmail.com',
            'password' => Hash::make('password'),
            'type' => 'admin',
        ]);
    }
}
