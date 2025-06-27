<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NamePrefixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('name_prefixes')->insert([
            ['prefix' => 'Prof. Dr.', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Prof.', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Dr.', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Dr (PhD).', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Er.', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Mr.', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Ms.', 'created_at' => now(), 'updated_at' => now()],
            ['prefix' => 'Mrs.', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
