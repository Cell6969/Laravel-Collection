<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EachTest extends TestCase
{
    public function testEach()
    {
        $this->view('each', ['users' => [
            [
                'name' => 'alphonso',
                'hobbies' => ['Coding', 'Gaming']
            ],
            [
                'name' => 'jonathan',
                'hobbies' => ['Coding', 'Gaming']
            ]
        ]])
            ->assertSeeInOrder([
                '.red',
                'alphonso',
                'Coding',
                'Gaming',
                'jonathan',
                'Coding',
                'Gaming'
            ]);
    }
}
