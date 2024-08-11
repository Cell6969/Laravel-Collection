<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IncludeConditionTest extends TestCase
{
    public function testIncludeCondition()
    {
        $this->view('include-condition', [
            'user' => [
                'name' => 'donovan',
                'admin' => 'salah'
            ]
        ])->assertDontSeeText('Selamat Datang Owner')
            ->assertSeeText('Selamat datang donovan');

        $this->view('include-condition', [
            'user' => [
                'name' => 'alphonso',
                'admin' => 'benar'
            ]
        ])->assertSeeText('Selamat Datang Owner')
            ->assertSeeText('Selamat datang alphonso');
    }
}
