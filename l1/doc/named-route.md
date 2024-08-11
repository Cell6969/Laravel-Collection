# Named Route

Named route kadang diperlukan pada proses pengembangan web misal untuk mendapatkan informasi tentang route tersebut atau melakukan redirect.

Contoh:

```php
Route::get('/products/{id}', function ($productId) {
    return "Product $productId";
})->name("product.detail");

Route::get('/products/{product}/items/{item}', function ($productId, $itemId) {
    return "Product $productId dan Item $itemId";
})->name("product.item.detail");

Route::get('/category/{id}', function ($categoryId) {
    return "Category $categoryId";
})->where('id', '[0-9]+')->name("category.detail");

Route::get('/users/{id?}', function ($userId = 'xz') {
    return "User id $userId";
})->name("user.detail");
```

Lalu untuk fungsinya:
```php
Route::get('produk/{id}', function ($id) {
    $link = route('product.detail', ['id' => $id]);
    return "Link $link";
});

Route::get('/product-redirect/{id}', function ($id) {
    return redirect()->route('product.detail', ['id' => $id]);
});
```

Jadi dengan cara sperti ini memudahkan untuk penulisan url route.
