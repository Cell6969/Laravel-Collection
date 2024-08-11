# Middleware

Di laravel banyak sekali menggunakan middleware, mulai dari enkripsi cookie, verifikasi CSRF, authentication, dan lain-lain. Semua middleware ada pada foler */app/Http/Middleware*.

Untuk membuat middleware :
```
php artisan make:middleware {name middleware}
```
Contoh, misal kita buat middleware untuk mendeteksi apakah terdapat api key atau tidak:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContohMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->headers('X-API-KEY');
        if ($apiKey == 'var') {
            return $next($request);
        } else {
            return response("Access Denied", 401);
        }
    }
}

```

Kemudian setelah perlu diregistrasikan terlebih dahulu. Ada 2 cara untuk registrasi, pertama secara global dimana nantinya smua route akan terkena middleware tsb, atau spesifik route.

## Spesifik route
Untuk spesifik route, pertama kita perlu buat aliasing di Kernel->protected routeMiddleware
```php
protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'contoh' => ContohMiddleware::class,
    ];
```
Lalu implementasi pada routenya:
```php
Route::get('/middleware/api', function () {
    return "Ok";
})->middleware(["contoh"]);
```
Dengan demikian, route /middleware/api akan dibatasi oleh middleware 'contoh' dimana 'contoh' merupaka Class middleware **ContohMiddleware**. Kemudian unit test:
```php
public function testMiddlewareInvalid()
{
    $this->get('/middleware/api')
        ->assertStatus(401)
        ->assertSeeText("Access Denied");
}
public function testMiddlewareValid()
{
    $this->withHeader("X-API-KEY", "var")
        ->get('/middleware/api')
        ->assertStatus(200)
        ->assertSeeText('Ok');
}
```
## Middleware Group
Di laravel, juga mendukung untuk registrasi middleware di level group. Contoh:
```php
protected $middlewareGroups = [
        'gc' =>[
            ContohMiddleware::class,
        ],
        ....
]
```
Untuk implementasinya:
```php
Route::get('/middleware/group', function () {
    return "Group";
})->middleware(['gc']);
```
Dengan demikian, semua middleware pada group 'gc' akan dipanggil untuk route tersebut.

## Middleware Parameter
Dilaravel kita dapat menggunakan handle() method untuk parameter di middleware. 

Sebagai contoh, kita edit middleware sebelumnya yang telah dibuat:
```php
public function handle(Request $request, Closure $next, string $key, int $status)
    {
        $apiKey = $request->header('X-API-KEY');
        if ($apiKey == $key) {
            return $next($request);
        } else {
            return response("Access Denied", $status);
        }
    }
```

Kemudian untuk melempar parameternya:
```php
Route::get('/middleware/api', function () {
    return "Ok";
})->middleware(["contoh:ArV,401"]);
```

untuk di group, perlu diupdate menjadi:
```php
protected $middlewareGroups = [
        'gc' =>[
            'contoh:ArV,401'
        ],
        ....
]
```
Jadi simplenya, kita harus menyebut nama alias, kemudian diikuti parameter nya secara berurut. Jadi direkomendasikan adalahh membuat alias untuk middleware.

## Exclude Middleware
Terkadang ada beberapa route yang ingin kita exclude middleware tertentu. Hal ini bisa dilakukan seperti ini:
```php
Route::post('/file/upload', [FileController::class, 'upload'])
    ->withoutMiddleware([VerifyCsrfToken::class]);
```