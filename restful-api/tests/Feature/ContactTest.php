<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateContactSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john@doe.com",
            "phone" => "0123456789",
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "John",
                    "last_name" => "Doe",
                    "email" => "john@doe.com",
                    "phone" => "0123456789",
                ]
            ]);
    }

    public function testCreateContactFail()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            "first_name" => "",
            "last_name" => "",
            "email" => "",
            "phone" => "",
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertSee("errors");
    }

    public function testGetSuccess()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class
        ]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => $contact->first_name,
                    "last_name" => $contact->last_name,
                    "email" => $contact->email,
                    "phone" => $contact->phone,
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class
        ]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . $contact->id + 1, [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertSee("errors");
    }

    public function testGetOtherUserContact()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class
        ]);

        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/$contact->id", [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertSee("errors");
    }

    public function testUpdateSuccess()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class
        ]);

        $data = [
            'first_name' => "update",
            'last_name' => "update",
            'email' => "update@email.com",
            'phone' => "0123456789",
        ];

        $contact = Contact::query()->limit(1)->first();
        $this->put("/api/contacts/$contact->id", $data, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "update",
                    "last_name" => "update",
                    "email" => "update@email.com",
                    "phone" => "0123456789",
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class
        ]);

        $user = User::query()->where('username', '=', 'test')->first();
        $contact = $user->contacts()->first();
        $this->delete("/api/contacts/$contact->id", [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testSearchContactByName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=test1', [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(1, count($response['data']));
        self::assertEquals(1, $response['meta']['total']);
    }


    public function testSearchContactByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test1@test', [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->json();

        self::assertEquals(1, count($response['data']));
        self::assertEquals(1, $response['meta']['total']);
    }

    public function testSearchContactByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=0123', [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response['data']));
        self::assertEquals(10, $response['meta']['total']);
    }

    public function testSearchByPageSize()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?page=1&size=5', [
            'Authorization' => "test"
        ])->assertStatus(200)
            ->json();


        Log::info(json_encode($response, JSON_PRETTY_PRINT));
        self::assertEquals(5, count($response['data']));
        self::assertEquals(10, $response['meta']['total']);
    }
}
