<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            'Makanan',
            'Minuman',
            'Snack',
            'Elektronik',
            'ATK',
            'Peralatan',
            'Lainnya'

        ];

        foreach ($data as $item) {

            Category::create([

                'name' => $item,

                'is_active' => true

            ]);

        }
    }
}