# View

Laravel menggunakan template engine blade untuk membuat code viewnya. Cara penamaany dengan blade yaitu "index.blade.php".

Sebagai contoh, buat file "welcome.blade.php"

```html
<html>
    <body>
        <h1>Hello {{$name}}</h1>
    </body>
</html>
```

Kemudian pada routing,

```php
Route::get('/helloworld', function () {
    return view('hello', [
        'name' => 'jonathan'
    ]);
});
```

Untuk unit test:

```php
public function testView()
    {
        $this->get('/helloworld')
            ->assertSeeText('Hello jonathan');
    }
```

## Nested View Directory

View juga bisa disimpan di dalam directory lagi didalam directory views. Hal ini baik ketika ada banyak views yang dimana bisa di manage lebih mudah.

Contoh , buat folder **hello** di views. Kemudian buat file **world.blade.php**

Pada file,

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ini world</title>
</head>
<body>
    <h1>Ini world oleh {{$name}}</h1>
</body>
</html>
```

Pada routing

```php
Route::get('/hello-world', function () {
    return view('hello.world', [
        'name' => 'jon'
    ]);
});
```

Pada code diatas, karena file world.blade berada dri folder hello maka cara aksesnya adalah hello.world

Kemudian untuk unit test,

```php
public function testNested()
    {
        $this->get('/hello-world')
            ->assertSeeText('Ini world oleh jon');
    }
```

## Optimizing Views

Untuk mengoptimize view, maka disarankan untuk mengcompile semua view yang sudah ada. Sehingga jika ada request maka tidak perlu lagi melakukan compile.

Command untuk mengcompile view

```
php artisan view:cache
```

Semua hasil compile view akan disimpan di folder _/storage/framework/views_. Sedangkan untuk menghapus,

```
php artisan view:clear
```

## Test View tanpa routing

Untuk melakukan test view tanpa routing , bisa dengan cara seperti ini

```php
public function testTemplate()
    {
        $this->view('hello', ['name' => 'lazard'])
            ->assertSeeText('Hello lazard');

        $this->view('hello', ['name' => 'edward'])
            ->assertSeeText('Hello edward');
    }
```
