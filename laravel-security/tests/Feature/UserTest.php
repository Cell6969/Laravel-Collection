<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testAuthentication()
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $success = Auth::attempt([
            "email" => "aldo@gmail.com",
            "password" => "aldo123"
        ], true);

        self::assertTrue($success);

        $user = Auth::user();
        self::assertNotNull($user);
        Log::info(json_encode($user, JSON_PRETTY_PRINT));
    }

    public function testGuest()
    {
        $user = Auth::user();
        self::assertNull($user);
    }

    public function testLogin()
    {
        $this->seed([UserSeeder::class]);

        $this->get("/users/login?email=aldo@gmail.com&password=aldo123")
            ->assertStatus(302)
            ->assertRedirect("/users/current");
    }

    public function testCurrent()
    {
        $this->seed([UserSeeder::class]);

        // test if user not authenticated
        $this->get("/users/current")
            ->assertSeeText("Hello Guest");

        $user = User::query()->where("email", "=", "aldo@gmail.com")->first();

        $this->actingAs($user)
            ->get("/users/current")
            ->assertSeeText("Hello aldo");
    }


}
