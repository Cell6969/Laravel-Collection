<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IfTest extends TestCase
{
    public function testIf()
    {
        $this->view('if', [
            'hobbies' => []
        ])->assertSeeText('I dont have any hobbies');

        $this->view('if', [
            'hobbies' => ['games']
        ])->assertSeeText('I just only have one hobbies.');

        $this->view('if', [
            'hobbies' => ['football', 'soccer']
        ])->assertSeeText('I have many hobbies');
    }
}
