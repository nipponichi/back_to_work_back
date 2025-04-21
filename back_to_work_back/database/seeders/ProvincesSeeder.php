<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            'Alava', 'Albacete', 'Alicante', 'Almeria', 'Asturias', 'Avila',
            'Badajoz', 'Barcelona', 'Burgos', 'Caceres', 'Cadiz', 'Cantabria',
            'Castellon', 'Ciudad Real', 'Cordoba', 'Cuenca', 'Girona', 'Granada',
            'Guadalajara', 'Guipuzcoa', 'Huelva', 'Huesca', 'Islas Baleares', 
            'Jaen', 'La Coruña', 'La Rioja', 'Las Palmas', 'Leon', 'Lleida',
            'Lugo', 'Madrid', 'Malaga', 'Murcia', 'Navarra', 'Ourense', 'Palencia',
            'Pontevedra', 'Salamanca', 'Santa Cruz de Tenerife', 'Segovia', 'Sevilla',
            'Soria', 'Tarragona', 'Teruel', 'Toledo', 'Valencia', 'Valladolid',
            'Vizcaya', 'Zamora', 'Zaragoza', 'Ceuta', 'Melilla'
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
