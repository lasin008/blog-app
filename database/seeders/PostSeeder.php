<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $users = User::pluck('id')->toArray();
        foreach (range(1, 10) as $index) {
            Post::create([
                'title' => $faker->word,
                'content' => $faker->paragraph,
                'author_id' => $faker->randomElement($users),
                'image' => null,
                'is_active' => $faker->boolean,
            ]);
        }
    }
}
