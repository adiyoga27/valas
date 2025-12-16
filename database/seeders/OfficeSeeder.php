<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::create([
            'name' => 'PT MONICA SEJAHTERA',
            'address' => 'Jln danau tamblingan no 73 Sanur, Denpasar Selatan, Bali',
            'phone' => '0822-3642-8223',]);
    }
}
