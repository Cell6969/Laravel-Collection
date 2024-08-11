<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ForTest extends TestCase
{
    public function testFor()
    {
        $this->view('for', [
            'limit' => 10
        ])->assertSeeText('0')->assertSeeText('9');
    }

    public function testForEach()
    {
        $this->view('forEach', [
            'hobbies' => ['Coding', 'Reading', 'Gaming']
        ])->assertSeeText('Coding')->assertSeeText('Gaming');
    }

    public function testForElse()
    {
        $this->view('forEach', [
            'hobbies' => []
        ])->assertSeeText('Tidak ada Hobi');

        $this->view('forEach', [
            'hobbies' => ['Coding', 'Reading', 'Gaming']
        ])->assertSeeText('Coding')->assertSeeText('Gaming')
            ->assertDontSeeText('Tidak ada Hobi');
    }
}
