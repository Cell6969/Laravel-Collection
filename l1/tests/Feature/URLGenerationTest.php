<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class URLGenerationTest extends TestCase
{
    public function testURLCurrent()
    {
        $this->get('/url/current?name=don')
            ->assertStatus(200)
            ->assertSeeText('/url/current?name=don');
    }

    public function testNamed()
    {
        $this->get('/redirect/named')
            ->assertStatus(200)
            ->assertSeeText('/redirect/name/don');
    }

    public function testAction()
    {
        $this->get('/url/action')
        ->assertStatus(200)
        ->assertSeeText('/form');
    }
}
