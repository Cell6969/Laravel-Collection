<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Tag;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tag = new Tag();
        $tag->id = "ig";
        $tag->name = "ig";
        $tag->save();

        $product = Product::query()->find("1");
        $product->tags()->save($tag);

        $voucher = Voucher::query()->first();
        $voucher->tags()->save($tag);
    }
}
