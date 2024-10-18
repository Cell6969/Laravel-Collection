<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    // Test Register
    public function testRegisterSuccess()
    {
        $data = [
            'username' => 'aldo',
            'password' => 'admin123',
            'name' => 'aldo donovan'
        ];

        $this->post('/api/users', $data)->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => $data['username'],
                    'name' => $data['name'],
                ]
            ]);
    }

    public function testRegisterFail()
    {
        $data = [
            'username' => '',
            'password' => '',
            'name' => ''
        ];

        $this->post('/api/users', $data)->assertStatus(400)
            ->assertSee('errors');
    }

    public function testRegisterUsernameAlreadyExists()
    {
        $this->testRegisterSuccess();

        $data = [
            'username' => 'aldo',
            'password' => 'admin123',
            'name' => 'aldo donovan'
        ];

        $this->post('/api/users', $data)->assertStatus(400)
            ->assertSee('errors')
            ->assertJson([
                'errors' => [
                    'username' => ["username already exists"]
                ]
            ]);
    }

    // Test Login
    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);

        $data = [
            'username' => 'test',
            'password' => 'test',
        ];

        $this->post('/api/users/login', $data)->assertStatus(200)
            ->assertSee('data')
            ->assertSee('token')
            ->assertJson([
                'data' => [
                    'username' => $data['username'],
                    'name' => 'test',
                ]
            ]);

        $user = User::query()->where('username', $data['username'])->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailedUsernameNotFound()
    {
        $data = [
            'username' => 'test',
            'password' => 'test',
        ];

        $this->post('/api/users/login', $data)->assertStatus(401)
            ->assertSee('errors')
            ->assertJson([
                "errors" => [
                    "message" => [
                        "invalid credentials"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordNotMatch()
    {
        $this->seed([UserSeeder::class]);
        $data = [
            'username' => 'test',
            'password' => 'salah',
        ];

        $this->post('/api/users/login', $data)->assertStatus(401)
            ->assertSee('errors')
            ->assertJson([
                "errors" => [
                    "message" => [
                        "invalid credentials"
                    ]
                ]
            ]);
    }

    // Test Get User
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->get('/api/users/current', [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ]
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->get('/api/users/current', [])->assertStatus(401)
            ->assertSee('errors');
    }

    public function testGetInvalidToken()
    {
        $this->get('/api/users/current', [
            'Authorization' => 'tokensalah'
        ])->assertStatus(401)
            ->assertSee('errors');
    }

    // Test Update User
    public function testUpdateName()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::query()->where('username', '=', 'test')->first();

        $this->patch('/api/users/current',
            [
                'name' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    "username" => "test",
                    "name" => "baru",
                ]
            ]);
        $newUser = User::query()->where('username', '=', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePassword()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::query()->where('username', '=', 'test')->first();

        $this->patch('/api/users/current',
            [
                'password' => 'baru'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    "username" => "test",
                    "name" => "test",
                ]
            ]);
        $newUser = User::query()->where('username', '=', 'test')->first();
        self::assertNotEquals(Hash::make($oldUser->password), Hash::make($newUser->password));
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->patch('/api/users/current',
            [
                'password' => 'scsasfinsoifnsfffffffffffffffffffffffffffffffffffffffffffffffffffffffsssssssssssssssssssssssssssssssssssssssssssssssssssssssssss'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertSee('errors');
    }

    public function testLogoutSuccess()
    {
        $this->seed([UserSeeder::class]);

        $user = User::query()->where('username', '=', 'test')->first();

        $this->delete('/api/users/logout', [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }


    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete('/api/users/logout', [], [
            'Authorization' => 'salah token'
        ])->assertStatus(401)
            ->assertSee('errors')
            ->assertJson([
                "errors" => [
                    "message" => "Unauthorized"
                ]
            ]);
    }
}
