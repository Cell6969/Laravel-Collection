<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewTest extends TestCase
{
    public function testView()
    {
        $this->get('/helloworld')
            ->assertSeeText('Hello jonathan');
    }

    public function testNested()
    {
        $this->get('/hello-world')
            ->assertSeeText('Ini world oleh jon');
    }

    public function testTemplate()
    {
        $this->view('hello', ['name' => 'lazard'])
            ->assertSeeText('Hello lazard');

        $this->view('hello', ['name' => 'edward'])
            ->assertSeeText('Hello edward');
    }
}
