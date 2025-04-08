<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 3 specific users with fixed emails
        User::create([
            'name' => 'Admin User',
            'email' => 'lasin@example.com',
            'password' => bcrypt('password1'), 
        ]);

        User::create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password1'),
        ]);

        User::create([
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'password' => bcrypt('password1'),
        ]);
    }
}
