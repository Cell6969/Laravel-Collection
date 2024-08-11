# Redirect

Contoh redirect laravel
```php
 public function redirectTo(): string
{
    return "Redirect to";
}
public function redirectFrom(): RedirectResponse
{
    return redirect("/redirect/to");
}
```
Lalu untuk routenya:
```php
Route::get('/redirect/from', [RedirectController::class,'redirectFrom']);
Route::get('/redirect/to',[RedirectController::class,'redirectTo']);
```
Ketika dites, pada saat akses */redirect/from* maka secara langsung akan redirect ke path */redirect/to*.

## Redirect to Named Routes
Laravel juga bisa melakukan redirect ke routes berdasarkan namanya, salah satu keuntungannya adalah kita bisa menambahkan parameter tanpa harus manual membuat pathnya.

Contoh case:
```php
public function redirectHello(string $name): string
    {
        return "Hello $name";
    }

public function redirectName(): RedirectResponse
{
    return redirect()->route('redirect-hello', [
        "name" => "jonathan"
    ]);
}
```
Kemudian pada routenya:
```php
Route::get('/redirect/name',[RedirectController::class,'redirectName']);
Route::get('/redirect/name/{name}',[RedirectController::class,'redirectHello'])
    ->name('redirect-hello');
```
Perlu diperhatikan ketika, membuat route */redirect/name/{name}*, kita harus membuat nama routenya,contoh disini 'redirect-hello'. Karena sudah terbuat maka bisa diakses dari route name controller.

## Redirect to Controller Action
Selain menggunaka named routes, bisa juga dilakukan redirect ke controller action. Secara otomatis laravel akan mencari path yang sesuai dengan Controller Action tsb.

Contoh:
```php
public function redirectAction(): RedirectResponse
{
    return redirect()->action([RedirectController::class, 'redirectHello'], [
        'name' => 'jonathan'
    ]);
}
```
Pada code controller diatas, akan meredirect ke controller lain (route lain) tanpa menyebut nama routenya.

## Redirect to External Domain
Untuk redirect dengan domain yang berbeda maka harus menyebut full domainnya baru pathnya, lalu untuk function harus men Contoh:
```php
public function redirectAway(): RedirectResponse
{
    return redirect()->away('https://youtube.com');
}
```
