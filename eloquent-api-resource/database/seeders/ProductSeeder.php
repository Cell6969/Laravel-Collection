<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::all()->each(function (Category $category) {
            for ($i = 0; $i < 10; $i++) {
                $category->product()->create([
                    "name" => "Product $i of $category->name",
                    "price" => rand(1, 100),
                ]);
            }
        });
    }
}
