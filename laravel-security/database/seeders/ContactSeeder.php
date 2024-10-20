<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->where("email", "aldo@gmail.com")->first();

        $contact = new Contact();
        $contact->name = "Aldo Contact";
        $contact->email = "aldo@gmail.com";
        $contact->user_id = $user->id;
        $contact->save();
    }
}
