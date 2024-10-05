<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = new Customer();
        $customer->id = "ALDO";
        $customer->name = "Aldo";
        $customer->email = "aldo@gmail.com";
        $customer->save();
    }
}
