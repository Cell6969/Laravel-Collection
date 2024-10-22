<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Database\Seeders\TodoSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    public function testTodo()
    {
        $this->seed([UserSeeder::class, TodoSeeder::class]);

        // user not login: failed
        $this->post("/api/todo")
            ->assertStatus(403);

        // user login: success
        $user = User::query()->where("email", "=", "aldo@gmail.com")->first();
        Auth::login($user);

        $this->post("/api/todo")
            ->assertStatus(201);
    }

    public function testView()
    {
        $this->seed([UserSeeder::class, TodoSeeder::class]);

        $user = User::query()->where("email", "=", "aldo@gmail.com")->first();

        Auth::login($user);

        $todos = Todo::query()->get();

        $this->view("todos", [
            "todos" => $todos
        ])->assertSeeText("Edit")
            ->assertSeeText("Delete")
            ->assertDontSeeText("No Edit")
            ->assertDontSeeText("No Delete");
    }
}
