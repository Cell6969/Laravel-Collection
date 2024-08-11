# Route Parameter

Laravel mendukung parameter url yang dinamis dalam pembuatan web app. Contoh pengguanaan
```php
Route::get('/products/{id}', function($productId){
    return "Product $productId";
});

Route::get('/products/{product}/items/{item}',function($productId, $itemId){
    return "Product $productId dan Item $itemId";
});
```

## Constrain Parameter
Kadang beberapa parameter merupakan tipe data number ketika di proses untuk selanjutnya. Untuk memparsing parameter bisa dilakukan dengan Regex. Contoh:
```php
Route::get('/category/{id}', function ($categoryId) {
    return "Category $categoryId";
})->where('id', '[0-9]+');
```
Pada code diatas juga bisa ditambahkan kondisi regexnya sesuai kebutuhan.

## Optional Route Parameter
Beberapa kasus optional parameter dibutuhkan, artinya bisa jadi wajib ada namun perlu diset default valuenya. Contoh
```php

```