# URL - Generation

url generation digunakan ketika ingin mengetahui url yang sedang diakses tetapi dengan catatan jika tidak ada class Request. Contoh:
```php
Route::get('/url/current', function(){
    return URL::full();
});
```
Kemudian untuk unit testnya:
```php
 public function testURLCurrent()
    {
        $this->get('/url/current?name=don')
            ->assertStatus(200)
            ->assertSeeText('/url/current?name=don');
    }
```

## Named Route
bisa juga kasusnya jika menggunakan named route. Contoh:
```php
Route::get('/redirect/name/{name}', [RedirectController::class, 'redirectHello'])
    ->name('redirect-hello');
Route::get('/redirect/named', function(){
    // Cara Pertama
    // return route('redirect-hello',['name' => 'jon']);
    
    // Cara Kedua
    // return url()->route('redirect-hello', ['name' => 'don']);

    // Cara Ketiga
    return URL::route('redirect-hello', ['name' => 'don']);
});
```

Kemudian pada unit testnya:
```php
public function testNamed()
    {
        $this->get('/redirect/named')
            ->assertStatus(200)
            ->assertSeeText('/redirect/name/don');
    }
```
## Route Untuk Controller Action
URLGenerator bisa digunakan untuk membuat link menuju controller action. Contoh implementasinya
```php
Route::get('/url/action', function () {
    // Cara Pertama
    // return action([FormController::class,'form'],[]);

    // Cara Kedua
    // return url()->action([FormController::class, 'form'],[]);

    // Cara Ketiga
    return URL::action([FormController::class, 'form'], []);
});
```

Untuk unit testnya:
```php
public function testAction()
    {
        $this->get('/url/action')
        ->assertStatus(200)
        ->assertSeeText('/form');
    }
```
Mengapa return urlnya adalah form? karena pada controller form akan mereturn view 'form'.