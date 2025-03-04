<?php

namespace Database\Seeders;

use App\Models\Adds;

use Illuminate\Database\Seeder;
class AddsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Adds::factory()->count(10)->create();
    }
}
