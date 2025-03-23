<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AddCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('adds_categories')->insert([
            [
                'category' => 'Cuidados Hogar',
                'description' => 'Servicios de hogar',
        
            ],
            [
                'category' => 'Clases particulares',
                'description' => 'Servicios de clases particulares',
        
            ],
            [
                'category' => 'Cuidados mascotas',
                'description' => 'Servicios de cuidados mascotas',
        
            ],
            [
                'category' => 'Reparaciones',
                'description' => 'Servicios de reparaciones',
        
            ],
    
        ]);
    }
}
