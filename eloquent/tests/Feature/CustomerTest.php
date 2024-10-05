<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Wallet;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\VirtualAccountSeeder;
use Database\Seeders\WalletSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

    public function testOneToOneQuery()
    {
        $customer = new Customer();
        $customer->id = "ALDO";
        $customer->name = "Aldo";
        $customer->email = "aldo@gmail.com";
        $customer->save();

        // insert data to wallet of customer
        $wallet = new Wallet();
        $wallet->amount = 1_000_000;
        $customer->wallet()->save($wallet);

        self::assertNotNull($wallet->customer_id);
    }

    public function testHasOneThrough()
    {
        // seed data
        $this->seed([
            CustomerSeeder::class,
            WalletSeeder::class,
            VirtualAccountSeeder::class
        ]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);

        $virtualAccount = $customer->virtualAccount;
        self::assertNotNull($virtualAccount);
        self::assertEquals('BCA', $virtualAccount->bank);
        Log::info($virtualAccount);
    }
}
