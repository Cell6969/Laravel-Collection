<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RatelimiterTest extends TestCase
{
    public function testRatelimiter()
    {
        $success = RateLimiter::attempt('send-message-1', 5, function (){
           echo "send message";
        });

        self::assertTrue($success);
    }
}
