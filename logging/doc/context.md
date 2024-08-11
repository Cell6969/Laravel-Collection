# Context

Di monolog terdapat fitur yaitu Context dan bisa digunakan pada Logging. Pada Log Facade, parameter kedua bisa diisi dengan data context.

Contoh:
```php
public function testContext()
    {
        Log::info("Helli info", ["user" => "jonathan"]);

        self::assertTrue(true);
    }
```

## With Context

Ada beberapa momen dimana context yang diberikan akan selalu sama dalam 1 lifecycle , untuk mempermudah maka bisa menggunakan with context.
```php
public function testWithContext()
    {
        Log::withContext(["user" => "jonathan"]);

        Log::info("access");
        Log::info("access");
        Log::info("access");

        self::assertTrue(true);
    }
```

Dengan demikian context yang dimasukkan akan selalu sama dalam setiap log.