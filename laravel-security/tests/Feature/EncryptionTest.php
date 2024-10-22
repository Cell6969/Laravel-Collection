<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class EncryptionTest extends TestCase
{
    public function testEncryption()
    {
        $value = "ini data rahasia";
        $encrypted = Crypt::encryptString($value);
        var_dump($encrypted);

        $decrypted = Crypt::decryptString($encrypted);
        var_dump($decrypted);

        self::assertEquals($value, $decrypted);
    }
}
