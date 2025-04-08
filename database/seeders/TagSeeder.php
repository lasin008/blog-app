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
        // Insert exactly three tags into the tags table
        $tags = [
            'Technology',
            'Health',
            'Education'
        ];

        // Loop through each tag and insert it into the database
        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag
            ]);
        }
    }
}
