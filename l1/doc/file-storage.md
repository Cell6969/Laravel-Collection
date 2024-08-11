# File Storage

Laravel mendukung abstraction untuk management File Storage menggunakan library Flysystem. Dengan library ini kita dapat menyimpan file di local maupun s3 AWS. Untuk config storage di laravel, terdapat pada */config/filesystem.php*.

Sebagai contoh, untuk unittest:
```php
public function testStorage()
    {
        $filesystem = Storage::disk("local");

        $filesystem->put("file.txt", "jonathan joe"); // untuk membuat file dan menaruhnya 

        $content = $filesystem->get("file.txt"); // untuk mengambil isi konten pada file

        assertEquals("jonathan joe", $content);
    }
```

## Storage Link
Laravel memiliki fitur yaitu Storage Link, dimana kita bisa membuat link dari */storage/app/public* ke */public/storage*. Dengan demikian file yang terdapat di File Storage Public bisa diakses via web. 

Untuk membuat link, maka bisa gunakan perintah:
```php
php artisan storage:link
```

Lalu file yang sudah dibuat akan muncul di folder public dan dapat diakses.
