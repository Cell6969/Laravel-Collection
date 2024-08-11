# Handler

Saat menggunakan driver monolog, kita bisa menentukan attribute handler yang berisi class Monoloh Handler. Contoh sebelumnya terdapat driver single untuk menyimpan data log ke file, sebner bisa menggunakan driver monolog dan handler StreamHandler. Contoh implementasinya
```php
...
 'file' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => storage_path("logs/application.log"),
            ],
        ],
...
```

Pada unit test:
```php
public function testFileHandler()
    {
        $filelogger = Log::channel('file');
        $filelogger->info("info");
        $filelogger->warning("warning");
        $filelogger->error("error");
        $filelogger->critical("critical");


        self::assertTrue(true);

    }
```

Jadi jika ingin membuat channel sendiri bisa seperti contoh code diatas.