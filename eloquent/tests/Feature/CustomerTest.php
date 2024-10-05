<?php

namespace Tests\Feature;

use App\Models\Customer;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function testOneToOne()
    {
        // seed data
        $this->seed([
            CustomerSeeder::class,
            WalletSeeder::class
        ]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);

        // get wallet
        $wallet = $customer->wallet;

        var_dump($wallet->amount);

    }
}
