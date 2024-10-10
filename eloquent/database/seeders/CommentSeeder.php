<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createCommentsForProducts();
        $this->createCommentsForVoucher();
    }

    private function createCommentsForProducts(): void
    {
        $product = Product::query()->find("1");

        $comment = new Comment();

        $comment->email = "aldo@gmail.com";
        $comment->title = "title";
        $comment->commentable_id = $product->id;
        $comment->commentable_type = Product::class;
        $comment->save();
    }

    private function createCommentsForVoucher()
    {
        $voucher = Voucher::query()->first();

        $comment = new Comment();

        $comment->email = "aldo@gmail.com";
        $comment->title = "title";
        $comment->commentable_id = $voucher->id;
        $comment->commentable_type = Voucher::class;
        $comment->save();
    }
}
