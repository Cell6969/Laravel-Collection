# Request

Request dapat dimasukkan pada input method controller. Sebagai contoh:
```php
 public function hello(Request $request,string $name): string
    {
        return $this->helloService->hello($name);
    }
```
Secara otomatis laravel akan mengenali request yang merupakan dependency object dari Request tanpa mengganggu parameter.

Sebagai contoh beberapa fungsi dari Request:
```php
public function request(Request $request): string
    {
        return $request->path() . PHP_EOL .
            $request->url() . PHP_EOL .
            $request->fullUrl() . PHP_EOL .
            $request->method() . PHP_EOL .
            $request->header("Accept") . PHP_EOL;
    }
```