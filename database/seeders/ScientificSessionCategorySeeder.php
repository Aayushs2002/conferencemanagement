<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScientificSessionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('scientific_session_categories')->insert([
            ['category_name' => 'Oral Presentation', 'slug' => slugify('Oral Presentation'), 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Poster Presentation', 'slug' => slugify('Poster Presentation'), 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Keynote Speech', 'slug' => slugify('Keynote Speech'), 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'Panel Discussion', 'slug' => slugify('Panel Discussion'), 'created_at' => now(), 'updated_at' => now()],
            ['category_name' => 'General Activites', 'slug' => slugify('General Activites'), 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
