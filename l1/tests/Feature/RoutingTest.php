<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    public function testGet()
    {
        $this->get('/hello')
            ->assertStatus(200)
            ->assertSeeText('Hello World');
    }

    public function testRedirect()
    {
        $this->get('/world')
            ->assertRedirect('/hello');
    }

    public function testFallBack()
    {
        $this->get("/null")
            ->assertSeeText("Ups, sepertinya terjadi kesalahan. Silahkan hubungi Developer.");
    }
}
