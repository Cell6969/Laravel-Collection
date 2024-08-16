<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InheritanceTest extends TestCase
{
    public function testInheritance()
    {
        $this->view('child', [])
            ->assertSeeText('Nama Aplikasi - Halaman Utama')
            ->assertSeeText('Deskripsi Header')
            ->assertSeeText('Ini adalah content');
    }
}
