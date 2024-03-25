<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            ['name' => 'free'],
            ['name' => 'guest'],
            ['name' => 'premium'],
        ];

        foreach ($userTypes as $type) {
            UserType::create($type);
        }
    }
}
