<?php

namespace Tests\Feature;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function testCreateComment()
    {
        $comment = new Comment();
        $comment->email = "alo@gmail.com";
        $comment->title = "Sample title";
        $comment->comment = "Ini comment";

        $comment->save();

        self::assertNotNull($comment->id);
    }

    public function testDefaultAttributes()
    {
        $comment = new Comment();
        $comment->email = "alo@gmail.com";

        $comment->save();

        self::assertNotNull($comment->id);
        self::assertNotNull($comment->title);
        self::assertNotNull($comment->comment);
    }
}
