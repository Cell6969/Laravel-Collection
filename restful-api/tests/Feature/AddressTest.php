<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post("/api/contacts/$contact->id/addresses",
            [
                "street" => "bekasi",
                "city" => "bekasi",
                "province" => "bekasi",
                "country" => "bekasi",
                "postal_code" => "12345",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "street" => "bekasi",
                    "city" => "bekasi",
                    "province" => "bekasi",
                    "country" => "bekasi",
                    "postal_code" => "12345",
                ]
            ]);

    }

    public function testGetSuccess()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class, AddressSeeder::class
        ]);

        $address = Address::query()->limit(1)->first();

        $this->get("/api/contacts/$address->contact_id/addresses/$address->id", [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "province" => "test",
                    "country" => "test",
                    "postal_code" => "test",
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $this->put("/api/contacts/$address->contact_id/addresses/$address->id",
            [
                "street" => "bekasi",
                "city" => "bekasi",
                "province" => "bekasi",
                "country" => "bekasi",
                "postal_code" => "12345",
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "bekasi",
                    "city" => "bekasi",
                    "province" => "bekasi",
                    "country" => "bekasi",
                    "postal_code" => "12345",
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $address = Address::query()->limit(1)->first();

        $id = $address->id;

        $this->delete("/api/contacts/$address->contact_id/addresses/$id",
            [],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $address = Address::query()->find($id);
        self::assertNull($address);
    }

    public function testListAddress()
    {
        $this->seed([
            UserSeeder::class, ContactSeeder::class, AddressSeeder::class
        ]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->get("/api/contacts/$contact->id/addresses", [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertSee("data")
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));
    }
}
