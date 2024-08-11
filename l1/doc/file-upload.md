# File Upload

Laravel mendukung untuk upload file. Hal ini bisa dilakukan pada request method. File yang diupload akan disimpan pada storage.

Untuk contoh codenya:

```php
{
        // get file by key
        $picture = $request->file("picture");

        // store the file
        $picture->storePubliclyAs("pictures", $picture->getClientOriginalName(), "public");

        return "OK" . $picture->getClientOriginalName();
    }
```

Untuk unit testnya, laravel dapat melakukan mocking terhadap input file
```php

```
