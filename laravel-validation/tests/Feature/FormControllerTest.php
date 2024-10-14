<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    public function testLoginSuccess()
    {
        $response = $this->post('/form/login',[
            'username' => 'aldo',
            'password' => 'aldo'
        ]);

        $response->assertStatus(200);
    }

    public function testLoginFail()
    {
        $response = $this->post('/form/login',[
            'username' => '',
            'password' => ''
        ]);

        $response->assertStatus(400);
    }


    public function testFormSuccess()
    {
        $response = $this->post('/form',[
            'username' => 'aldo',
            'password' => 'aldo'
        ]);

        $response->assertStatus(200);
    }

    public function testFormFail()
    {
        $response = $this->post('/form',[
            'username' => '',
            'password' => ''
        ]);

        $response->assertStatus(302);
    }
}
