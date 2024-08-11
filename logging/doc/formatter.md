# Formatter

Di laravel juga bisa untuk mengatur format pada log entah berbentuk json atau yang lain. Contoh:
```php
'file' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => JsonFormatter::class,
            'with' => [
                'stream' => storage_path("logs/application.log"),
            ],
        ],
```

Nanti hasil lognya:
```json
{"message":"info","context":{},"level":200,"level_name":"INFO","channel":"testing","datetime":"2024-08-03T10:37:36.243936+00:00","extra":{}}
{"message":"warning","context":{},"level":300,"level_name":"WARNING","channel":"testing","datetime":"2024-08-03T10:37:36.244967+00:00","extra":{}}
{"message":"error","context":{},"level":400,"level_name":"ERROR","channel":"testing","datetime":"2024-08-03T10:37:36.245061+00:00","extra":{}}
{"message":"critical","context":{},"level":500,"level_name":"CRITICAL","channel":"testing","datetime":"2024-08-03T10:37:36.246758+00:00","extra":{}}
```