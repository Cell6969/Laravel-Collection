<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InputControllerTest extends TestCase
{
    public function testInput()
    {
        $this->get("/input/hello?name=don")
            ->assertStatus(200)
            ->assertSeeText("Hello don");

        $this->post('/input/hello', [
            'name' => 'don'
        ])->assertStatus(200)
            ->assertSeeText('Hello don');
    }

    public function testNestedInput()
    {
        $this->post('/input/hello/first', [
            "name" => [
                "first" => "don",
                "last" => "var"
            ]
        ])->assertStatus(200)
            ->assertSeeText("Hello don");
    }

    public function testAllInput()
    {
        $this->post('/input/hello/input', [
            "name" => [
                "first" => "jonathan",
                "last" => "Var"
            ]
        ])->assertStatus(200)->assertSeeText("name")
            ->assertSeeText("first")->assertSeeText("last")
            ->assertSeeText("jonathan")->assertSeeText("Var");
    }

    public function testInputArray()
    {
        $this->post('/input/hello/input', [
            "products" => [
                [
                    "name" => "Apple",
                    "cost" => 30
                ],
                [
                    "name" => "Samsung",
                    "cost" => 36
                ],
                [
                    "name" => "lenovo",
                    "cost" => 99
                ]
            ]
        ])->assertSeeText("Apple")
            ->assertSeeText("Samsung")
            ->assertSeeText("lenovo");
    }

    public function testInputType()
    {
        $this->post('/input/type', [
            "name" => "budi",
            "married" => 'true',
            'birth_date' => '1990-10-10'
        ])->assertSeeText("budi")->assertSeeText('true')->assertSeeText('1990-10-10');
    }

    public function testFilterOnly()
    {
        $this->post('/input/filter/only', [
            "name" => [
                "first" => "jonathan",
                "last" => "donovan",
                "middle" => "arga"
            ],
            "phone" => "0892222"
        ])->assertSeeText("jonathan")->assertSeeText("donovan")
            ->assertDontSeeText("arga")->assertDontSeeText("0892222");
    }

    public function testFilterExcept()
    {
        $this->post('/input/filter/except', [
            'username' => "arga",
            'password' => "12345",
            "admin" => "true"
        ])->assertSeeText("arga")->assertSeeText("12345")->assertDontSeeText('admin');
    }

    public function testFilterMerge()
    {
        $this->post('/input/filter/merge', [
            'username' => 'a',
            'password' => '123',
            'admin' => 'true'
        ])->assertSeeText('a')->assertSeeText('123')
            ->assertSeeText('admin')->assertSeeText('false');
    }
}
