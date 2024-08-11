# Log Facade

Untuk melakukan logging di laravel, bisa menggunakan Log Facade. Sebagai contoh untuk menggunakan Log Facade.
```php
public function testLogging()
    {
        Log::info("Ini Info");
        Log::warning("ini warning");
        Log::error("ini error");
        Log::critical("ini critical");

        self::assertTrue(true);
    }
```

Jadi tanpa harus membuat instance log , bisa menggunakan facade yang sudah tersedia. Log akan tersimpan di */storage/logs/laravel.log*.