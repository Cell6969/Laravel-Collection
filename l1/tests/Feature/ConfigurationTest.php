<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfig(): void
    {
        $firstname = config('contoh.name.first');
        $lastname = config('contoh.name.last');
        $email = config('contoh.email');
        $web = config('contoh.web');
        $youtube = config('contoh.youtube');

        self::assertEquals('jonathan', $firstname);
        self::assertEquals('vargan', $lastname);
        self::assertEquals('jonathan@exml.com', $email);
        self::assertEquals('https://jonathanweb.com', $web);
        self::assertEquals('don', $youtube);
    }
}
