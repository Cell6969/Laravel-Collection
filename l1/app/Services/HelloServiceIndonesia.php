<?php

namespace App\Services;

class HelloServiceIndonesia implements HelloService
{
    public function hello(string $name): string
    {
        return "Hello $name";
    }

    public function hello2(string $firstname, string $lastname): string
    {
        return "Hello $firstname $lastname";
    }
}
