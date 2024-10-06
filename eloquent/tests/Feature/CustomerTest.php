<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Wallet;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ImageSeeder;
use Database\Seeders\ProductSeeder;
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

    public function testManyToMany()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class, ProductSeeder::class]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);


        $customer->likeProducts()->attach("1");
        $customer->likeProducts()->attach("2");
        $products = $customer->likeProducts;
        self::assertCount(2, $products);

        self::assertEquals("1", $products[0]->id);
        self::assertEquals("2", $products[1]->id);
    }

    public function testManyToManyDetach()
    {
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        // detach products
        $customer->likeProducts()->detach("1");

        $products = $customer->likeProducts;
        self::assertCount(1, $products);
        Log::info($products);
    }

    public function testPivotAttribute()
    {
        // seed data
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        $products = $customer->likeProducts;

        foreach ($products as $product) {
            $pivot = $product->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);
            Log::info($pivot);
        }
    }

    public function testPivotAttributeCondition()
    {
        // seed data
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        $products = $customer->likeProductsLastWeek;
        foreach ($products as $product) {
            $pivot = $product->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);
        }
    }

    public function testPivotModel()
    {
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        $products = $customer->likeProducts;

        foreach ($products as $product) {
            $pivot = $product->pivot; // => pivot disini sudah berupa object Model Like
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);

            self::assertNotNull($pivot->customer);
            self::assertNotNull($pivot->product);
            Log::info($pivot);
        }
    }

    public function testOneToOnePolymorphic()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);

        $image = $customer->image;
        self::assertNotNull($image);
        self::assertEquals("https://image.com/1.jpg", $image->url);
    }
}
