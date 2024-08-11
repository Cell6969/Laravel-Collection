<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FacadeTest extends TestCase
{
    public function testConfig()
    {
        // menggunakan helper
        $firstname1 = config('contoh.name.firstname');

        // menggunakan facades
        $firstname2 = Config::get('contoh.name.firstname');

        self::assertEquals($firstname1, $firstname2);
    }

    public function testConfigDependency()
    {
        $config = $this->app->make('config');
        $firstname1 = config('contoh.name.firstname');
        $firstname2 = $config->get('contoh.name.firstname');

        self::assertEquals($firstname1, $firstname2);
    }

    public function testFacadedMock()
    {
        Config::shouldReceive('get')
            ->with('contoh.name.firstname')
            ->andReturn('jonathan');

        $firstname = Config::get('contoh.name.firstname');

        self::assertEquals('jonathan', $firstname);
    }
}
