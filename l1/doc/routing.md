# Routing

Routing adalah proses menerima HTTP Request dan menjalankan code sesuai dengan URL. Pada laravel, routing diatur oleh service provider yaitu **RouteServiceProvider**. **RouteServiceProvider** bertanggung jawab untuk melakukan load data routing dari folder routes.

Pada route terdapat 2 prefix yaitu web dan api. Untuk tampilan pada umumnya adalah web sedangkan prosess ke backend menggunakan api.

Contoh, edit **/routes/web.php**, seperti ini:

```php
Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return "Hello World";
});
```

Ketika dijalankan web browser, akses /hello makan akan muncul tulisan Hello World.

## Unit Test

Untuk unit test routing bisa dilakukan dengan contoh sebagai berikut:

```php
 public function testGet()
    {
        $this->get('/hello')
            ->assertStatus(200)
            ->assertSeeText('Hello World');
    }
```

## Redirect

Pada laravel bisa dilakukan redirect, contoh:

```php
Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return "Hello World";
});

Route::redirect('/world', '/hello');
```

Kemudian pada unit test

```php
public function testRedirect()
    {
        $this->get('/world')
            ->assertRedirect('/hello');
    }
```

## List Route

Pada laravel kita dapat melihat semua routing yang sudah dibuat.
Commandnya:

```
php artisan route:list
```

## Fallback Route

Fallback route merupakan suatu teknik dimana ketika dari sisi client mengakes path yang tidak terdaftar , dimana secara otomatis maka akan mengembalikan 404. Untuk melakukan customisasi maka bisa menggunakan FallbackRoute.

```php
Route::get('/', function () {
    return view('welcome');
});

Route::get('/hello', function () {
    return "Hello World";
});

Route::redirect('/world', '/hello');

Route::fallback(function () {
    return "Ups, sepertinya terjadi kesalahan. Silahkan hubungi Developer.";
});
```

Pada unit test:

```php
public function testFallBack()
    {
        $this->get("/null")
            ->assertSeeText("Ups, sepertinya terjadi kesalahan. Silahkan hubungi Developer.");
    }
```
