<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Health & Beauty'
        ];

        $subcategories = [
            'Makeup',
            'Skin Care'
        ];

        foreach ($categories as $category) {
            $category = Category::create([
                'name' => $category
            ]);
        }

        foreach ($subcategories as $subcategory) {
            $category = Category::create([
                'category_id' => 1,
                'name' => $subcategory
            ]);
        }
    }
}
