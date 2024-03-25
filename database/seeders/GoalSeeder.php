<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goals = [
            ['name' => 'Get Fitter'],
            ['name' => 'Gain Weight'],
            ['name' => 'Lose Weight'],
            ['name' => 'Gain More Flexible'],
            ['name' => 'Building Muscles'],
            ['name' => 'Others'],
        ];

        foreach ($goals as $goal) {
            Goal::create($goal);
        }
    }
}
