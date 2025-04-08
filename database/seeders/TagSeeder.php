<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag; // Import the Tag model

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            'Technology',
            'Health',
            'Education'
        ];
        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag
            ]);
        }
    }
}
