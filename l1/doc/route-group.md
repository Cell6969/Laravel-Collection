# Route Group

Laravel mendukung grouping route dimana dengan melakukan grouping route bisa share konfigurasi antar route dalam satu group.

Contoh implementasinya
```php
Route::prefix('/response/type')->group(function(){
    Route::get('/view', [ResponseController::class, 'responseView']);
    Route::get('/json', [ResponseController::class, 'responseJson']);
    Route::get('/file', [ResponseController::class, 'responseFile']);
});
```
## Route Middleware
Selain group di route, bisa juga dipasang middleware untuk route group. Contoh implementasinya
```php
Route::middleware(['contoh:Arv,401'])->group(function(){
    Route::get('/middleware/api', function(){
        return "Ok";
    });

    Route::get('/middleware/group', function(){
        return "Group";
    });
});
```

## Route Controller
Bisa juga kite men-group kan berdasarkan controller yang sama. Contoh implementasinya
```php
Route::controller(CookieController::class)->group(function () {
    Route::get('/cookie/set', 'createCookie');
    Route::get('/cookie/get', 'getCookie');
    Route::get('/cookie/clear', 'clearCookie');
});
```
Pada contoh diatas, ketika kita mengroupkan url berdasarkan controller maka method yang digunakan sesuai pada controllernya.

## Multiple Route Group
Terkadang ada beberapa case dimana kita ingin men-group kan bukan hanya 1 kondisi tapi multiple kondisi sebagai contoh:
```php
Route::middleware(['contoh:Arv,401'])->prefix('middleware')->group(function () {
    Route::get('api', function () {
        return "Ok";
    });

    Route::get('group', function () {
        return "Group";
    });
});
```
