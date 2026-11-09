<?php

namespace Database\Seeders;

use App\Models\MeasureUnit;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeasureUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MeasureUnit::create(['name' => 'PCS', 'ratio' => 1]);
        MeasureUnit::create(['name' => 'BOX', 'ratio' => 10]);
        MeasureUnit::create(['name' => 'DZN', 'ratio' => 12]);
        MeasureUnit::create(['name' => 'BAG', 'ratio' => 50]);
    }
}
