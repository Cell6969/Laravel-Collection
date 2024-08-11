# Cookie
Secara otomatis, cookie yang dibuat oleh laravel akan selalu di enkripsi dan ketika ingin membaca cookie, secara otomatis akan di dekrip. Semua dilakukan otomatis oleh class **App\Http\Middleware\EncryptCookies**. Untuk melakukan exceptional enckripsi maka bisa dilakukan dengan **$except**.

Berikut isi code EncryptCookies:
```php
<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
```

Untuk membuat cookie, bisa dengan contoh berikut:
```php
public function createCookie(Request $request): Response
{
    return response("hello cookie")
        ->cookie("userId", "jonathan", 1000, "/")
        ->cookie("isAdmin", "false", 1000, "/");
}
```
## Menerima Cookie
Setelah cookie disimpan di browser, cookie juga dapat di get dari class Request. Contoh:
```php
public function getCookie(Request $request): JsonResponse
{
    return response()
        ->json([
            "userId" => $request->cookie("userId", "guess"),
            "isAdmin" => $request->cookie("isAdmin", "false"),
        ]);
}
```

## Clear Cookie
Di laravel kita dapat membersihkan cookie dengan method withoutCookie(name).

Contoh:
```php
public function clearCookie(Request $request): Response
{
    return response("Clear Cookie")
        ->withoutCookie("userId")
        ->withoutCookie("isAdmin");
}
```