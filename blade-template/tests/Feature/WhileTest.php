<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WhileTest extends TestCase
{
    public function testWhile()
    {
        $this->view('while', [
            'i' => 0
        ])->assertSeeText('Current Value is 0')
            ->assertSeeText('Current Value is 1')
            ->assertSeeText('Current Value is 2')
            ->assertSeeText('Current Value is 3')
            ->assertSeeText('Current Value is 4')
            ->assertSeeText('Current Value is 5')
            ->assertSeeText('Current Value is 6')
            ->assertSeeText('Current Value is 7')
            ->assertSeeText('Current Value is 8')
            ->assertSeeText('Current Value is 9');
    }
}
