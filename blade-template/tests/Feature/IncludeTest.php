<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncludeTest extends TestCase
{
    public function testInclude()
    {
        $this->view('include', [])
            ->assertSeeText('Alphonso')
            ->assertSeeText('ini web');

        $this->view('include', [
            'title' => 'jonathan'
        ])->assertSeeText('jonathan')
            ->assertSeeText('ini web');
    }
}
