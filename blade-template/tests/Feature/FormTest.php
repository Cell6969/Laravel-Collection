<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormTest extends TestCase
{
    public function testForm()
    {
        $this->view('form', ['user' => [
            'premium' => true,
            'name' => 'alphonso',
            'admin' => true
        ]])
            ->assertSee('checked')
            ->assertSee('alphonso')
            ->assertDontSee('readonly');

        $this->view('form', ['user' => [
            'premium' => false,
            'name' => 'alphonso',
            'admin' => false
        ]])
            ->assertDontSee('checked')
            ->assertSee('alphonso')
            ->assertSee('readonly');
    }
}
