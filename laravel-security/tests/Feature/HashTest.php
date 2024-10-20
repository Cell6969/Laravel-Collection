<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class HashTest extends TestCase
{
    public function testHash()
    {
        $password = "secret";

        $hash = Hash::make($password);

        $result = Hash::check("secret", $hash);

        self::assertTrue($result);
    }

}
