<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Env;
use Tests\TestCase;

class EnvironmentTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetEnv()
    {
        $youtube = env('YOUTUBE');

        var_dump($youtube);

        self::assertEquals('don', $youtube);
        self::assertNotEquals('salah', $youtube);
    }

    public function testDefaultEnv(): void
    {
        $author = env('AUTHOR', 'mary');
        var_dump($author);

        self::assertEquals('mary', $author);
    }

    public function testUsingClass(): void
    {
        $youtube = Env::get('YOUTUBE', 'nothing');
        var_dump($youtube);

        self::assertEquals('don', $youtube);
    }
}
