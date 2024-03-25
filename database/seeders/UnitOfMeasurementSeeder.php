<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitOfMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Metric'],
            ['name' => 'Imperial'],
        ];

        foreach ($units as $unit) {
            UnitOfMeasurement::create($unit);
        }
    }
}
