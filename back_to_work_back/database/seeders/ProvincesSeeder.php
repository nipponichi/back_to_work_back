<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Álava', 'Albacete', 'Alicante', 'Almería', 'Asturias', 'Ávila',
            'Badajoz', 'Barcelona', 'Burgos', 'Cáceres', 'Cádiz', 'Cantabria',
            'Castellón', 'Ciudad Real', 'Córdoba', 'Cuenca', 'Girona', 'Granada',
            'Guadalajara', 'Guipúzcoa', 'Huelva', 'Huesca', 'Illes Balears', 
            'Jaén', 'La Coruña', 'La Rioja', 'Las Palmas', 'León', 'Lleida',
            'Lugo', 'Madrid', 'Málaga', 'Murcia', 'Navarra', 'Ourense', 'Palencia',
            'Pontevedra', 'Salamanca', 'Santa Cruz de Tenerife', 'Segovia', 'Sevilla',
            'Soria', 'Tarragona', 'Teruel', 'Toledo', 'Valencia', 'Valladolid',
            'Vizcaya', 'Zamora', 'Zaragoza'
        ];

        foreach ($provinces as $province) {
            DB::table('provinces')->insert([
                'name' => $province,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
