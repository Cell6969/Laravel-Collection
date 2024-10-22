<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class CacheTest extends TestCase
{
    public function testCache()
    {
        Cache::put("name", "aldo", 3);
        Cache::put("country", "Indonesia", 2);

        $response = Cache::get("name");
        self::assertEquals($response, "aldo");
        $response = Cache::get("country");
        self::assertEquals($response, "Indonesia");

        sleep(5);

        $response = Cache::get("name");
        self::assertNull($response);
        $response = Cache::get("country");
        self::assertNull($response);
    }

}
