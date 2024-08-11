<?php

namespace App\Services;

interface HelloService
{
    public function hello(string $name): string;

    public function hello2(string $firstname, string $lastname): string;
}
