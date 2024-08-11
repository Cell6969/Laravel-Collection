# Configuration

Konfigurasi pada laravel merupakan setup yang dilakukan ketika perubahan tidak terlalu sering. Untuk menambahkan file configuration maka cukup tambahkan file pada folder **config/**. Pada nama file config disarankan menggunakan huruf kecil semua.

Contoh pembuatan config. Pertama buat file **contoh.php** lalu taruh di **/config**.

```php
<?php

return [
    "name" => [
        "first" => "jonathan",
        "last" => "vargan"
    ],
    "email" => "jonathan@exml.com",
    "web" => "https://jonathanweb.com",
    "youtube" => env('YOUTUBE', 'youtube')
];
```

Cara memanggil config:
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfig(): void
    {
        $firstname = config('contoh.name.first');
        $lastname = config('contoh.name.last');
        $email = config('contoh.email');
        $web = config('contoh.web');
        $youtube = config('contoh.youtube');

        self::assertEquals('jonathan', $firstname);
        self::assertEquals('vargan', $lastname);
        self::assertEquals('jonathan@exml.com', $email);
        self::assertEquals('https://jonathanweb.com', $web);
        self::assertEquals('don', $youtube);
    }
}
```

Untuk melakukan cache pada config, maka bisa menggunakan command:
```
php artisan config:cache
```

By default, laravel akan membaca cache terlebih dahulu baru config file, oleh karenanya perlu di clear ketika terdapat perubahan pada config.

```
php artisan config:clear
```

