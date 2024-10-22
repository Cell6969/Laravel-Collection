<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class GuestTest extends TestCase
{
    public function testGuestNotLogin()
    {
        self::assertTrue(Gate::allows('create', User::class));
    }

    public function testGuestLogin()
    {
        $this->seed([UserSeeder::class]);

        $user = User::query()->where("email", "=", "aldo@gmail.com")->first();
        Auth::login($user);

        self::assertFalse(Gate::allows('create', User::class));
    }
}
