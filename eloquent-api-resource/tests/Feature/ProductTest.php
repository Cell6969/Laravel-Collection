<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    public function testProduct()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $product = Product::query()->first();

        $this->get("/api/products/$product->id")
            ->assertStatus(200)
            ->assertJson([
                "value" => [
                    "id" => $product->id,
                    "name" => $product->name,
                    "price" => $product->price,
                    "category" => [
                        "id" => $product->category->id,
                        "name" => $product->category->name,
                    ],
                    "created_at" => $product->created_at->toJSON(),
                    "updated_at" => $product->updated_at->toJSON(),
                ]
            ]);
    }

}
