<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where("username", '=', "test")->first();

        for ($i = 0; $i < 10; $i++) {
            Contact::query()->create([
               "first_name" => "test$i",
                "last_name" => "test$i",
                "email" => "test$i@test.com",
                "phone" => "01234567$i",
                "user_id" => $user->id,
            ]);
        }
    }
}
