<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class AppEnvironmentTest extends TestCase
{
    public function testAppEnv(): void
    {
        var_dump(App::environment()); // outputnya testing karena pada php unit , di hardcode menjadi "testing"

        if (App::environment('testing')) {
            self::assertTrue(true);
        }
    }
}
