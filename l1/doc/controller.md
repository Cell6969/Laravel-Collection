# Controller

Controller berfungsi untuk menyimpan logic route sehingga tidak perlu lagi dilakukan di route. Controller pada php direpresentasikan sebagai class seperti UserController, ProductController, CategoryController, dll.

Controller biasa disimpan pada _/App/Http/Controllers_. Untuk membuat controller bisa dengan commmand:

```
php artisan make:controller NamaController
```

Pada file controller, kita dapat membuat method seperti ini

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    public function hello(): string
    {
        return "hello world";
    }
}

```

Lalu untuk implementasinya:

```php
Route::get('/controller/hello', [HelloController::class, 'hello']);
```

## Dependency Injection

Karena controller merupakan basis class yang artinya pada controller tersebut dapat di Inject class - class lain, yang pada umumnya adalah class service. Sebagai contoh:

```php
<?php

namespace App\Http\Controllers;

use App\Services\HelloService;
use Illuminate\Http\Request;

class HelloController extends Controller
{
    private HelloServic $helloService;

    public function __construct(HelloService $helloService)
    {
        $this->helloService = $helloService;
    }

    public function hello(string $name): string
    {
        return $this->helloService->hello($name);
    }
}
```

Lalu pada web.php:
```php
Route::get('/controller/hello/{name}', [HelloController::class, 'hello']);
```

By default, laravel mengambil parameter pertama yang dijadikan sebagai input parameter pertama padad method.

