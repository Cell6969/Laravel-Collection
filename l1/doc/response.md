# Response

Pada laravel, di controller kita bisa mengembalikan data berupa string dan view. Untuk membuat object response, kita bisa menggunakan function helper response (content, status, headers).

Contoh sederhana:

```php
public function response(Request $request): Response
    {
        return response("hello response"); // urutannya (response content, status, header)
    }
```
## Header
Untuk merubah header response, maka bisa dilakukan seperti ini:
```php
public function header(Request $request): Response
    {
        $body = [
            "firstname" => 'don',
            "lastname" => 'var'
        ];

        return response(json_encode($body), Response::HTTP_OK)
        ->header('Content-Type','application/json')
        ->withHeaders([
            'Author' => 'XVargan',
            'App' => 'Laravel'
        ]);

    }
```
## Response Type
Beberapa response yang bisa ditampilkan menggunakan laravel antara lain response json, response view, file dll.

1. Response View
```php
public function responseView(Request $request): Response
    {
        return response()
            ->view('hello', [
                'name' => 'jonathan'
            ]);
    }
```

2. Response JSON
```php
public function responseJson(Request $request): JsonResponse
    {
        $body = [
            "firstname" => 'don',
            "lastname" => 'var'
        ];
        return response()
            ->json($body);
    }
```
3. Response File
```php
public function responseFile(Request $request): BinaryFileResponse
    {
        return response()
            ->file(storage_path('app/public/pictures/aida.jpg'));
    }

public function downloadFile(Request $request): BinaryFileResponse
    {
        return response()
            ->download(storage_path('app/public/pictures/aida.jpg'));
    }
```
Perbedaannya, klo response, hanya merender gambar, sedangkan donwload, merender sekaligus mendownload. 

