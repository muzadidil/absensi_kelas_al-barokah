<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Database\Seeder;

class GradeLevelSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['1st Year', '2nd Year', '3rd Year', '4th Year'] as $name) {
            GradeLevel::firstOrCreate(['name' => $name]);
        }

        foreach (['A', 'B', 'C', 'D'] as $name) {
            Section::firstOrCreate(['name' => $name]);
        }
    }
}
