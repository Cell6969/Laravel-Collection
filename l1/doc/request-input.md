# Request - Input

Untuk mengambil input yang pada http method, di laravel bisa menggunakan input function.

Contoh:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputController extends Controller
{
    public function hello(Request $request): string
    {
        $name = $request->input("name");
        return "Hello $name";
    }
}
```

Kemudian pada router:

```php
Route::get('/input/hello', [InputController::class, 'hello']);
Route::post('/input/hello', [InputController::class, 'hello']);
```

Untuk testing:

```php
public function testInput()
    {
        $this->get("/input/hello?name=don")
            ->assertStatus(200)
            ->assertSeeText("Hello don");

        $this->post('/input/hello', [
            'name' => 'don'
        ])->assertStatus(200)
            ->assertSeeText('Hello don');
    }
```

## Nested Input

pada laravel terdapat fitur canggih dimana dapat mengambil nested input dengan menggunakan titik.

Contoh:

```php
{
    $firstname = $request->input('first.name');
    return "Hello $firstname";
}
```

Untuk testing:

```php
public function testNestedInput()
    {
        $this->post('/input/hello/first', [
            "name" => [
                "first" => "don",
                "last" => "var"
            ]
        ])->assertStatus(200)
            ->assertSeeText("Hello don");
    }
```

Ketika ditest menggunakan postman atau sebagainya , terlebih dahulu untuk mematikan csrf protection. Untuk test nya bisa menggunakan formdata

## All Input

Untuk mengambil semua input baik dari query param ataupun body, bisa menggunakan input();
Contoh:

```php
public function helloInput(Request $request): string
    {
        $input = $request->input();
        return json_encode($input);
    }
```

Pada unit testnya:

```php
public function testAllInput()
    {
        $this->post('/input/hello/input', [
            "name" => [
                "first" => "jonathan",
                "last" => "Var"
            ]
        ])->assertStatus(200)->assertSeeText("name")
            ->assertSeeText("first")->assertSeeText("last")
            ->assertSeeText("jonathan")->assertSeeText("var");
    }
```

## Array Input

Laravel juga dapat mengambil semua value pada array input. Contoh:

```php
public function helloArray(Request $request): string
    {
        $names = $request->input("products.*.name");
        return json_encode($names);
    }
```

Pada code diatas, akan diambil semua array pada product kemudian key yang diambil adalah name.

untuk unit test:

```php
public function testInputArray()
    {
        $this->post('/input/hello/input', [
            "products" => [
                [
                    "name" => "Apple",
                    "cost" => 30
                ],
                [
                    "name" => "Samsung",
                    "cost" => 36
                ],
                [
                    "name" => "lenovo",
                    "cost" => 99
                ]
            ]
        ])->assertSeeText("Apple")
            ->assertSeeText("Samsung")
            ->assertSeeText("lenovo");
    }
```
