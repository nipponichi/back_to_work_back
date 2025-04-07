<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ads')->insert([
            [
                'name' => 'Venta de Cómics Antiguos',
                'description' => 'Colección de cómics clásicos en excelente estado.',
                'category_id' => 3,
                'due_date' => Carbon::now()->addDays(10)->toDateString(),
                'location' => 'Ciudad de México',
                'is_done' => false,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Intercambio de Mangas',
                'description' => 'Busco intercambiar mangas de One Piece y Naruto.',
                'category_id' => 1,
                'due_date' => Carbon::now()->addDays(15)->toDateString(),
                'location' => 'Buenos Aires',
                'is_done' => false,
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Venta de Figuras de Acción',
                'description' => 'Figuras de acción de Marvel y DC en perfecto estado.',
                'category_id' => 2,
                'due_date' => Carbon::now()->addDays(20)->toDateString(),
                'location' => 'Madrid',
                'is_done' => true,
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
