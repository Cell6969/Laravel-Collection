# Intro

## Validator
Validator adalah class sebagai representasi untuk melakukan validasi di laravel. Untuk menggunakan validator harus menggunakan dari facade Illuminate support facades. Untuk membuat validator kita harus membuat rules yang nanti digunakan untuk proses validasi.

Sebagai contoh:
```php
public function testValidator()
    {
        $data = [
            'username' => 'admin',
            'password' => 'admin',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);
    }
```
Cukup sederhana untuk membuat validator. Kemudian untuk membuat apakakah validasi itu success atau tidak bisa seperti ini:
```php
... 
self::assertTrue($validator->passes());
self::assertFalse($validator->fails());
```

## Message Error
Jikalau terjadi error pada proses validasi, maka kita bisa menangkap error yang terjadi.
```php
... 
$message = $validator->getMessageBag();
// get message from key
$message->get('username');
$message->get('password');
Log::info($message->toJson(JSON_PRETTY_PRINT));
```
dengan demikian dari validator bisa juga didapatkan message errornya.

## Validation Exception
Jikalau data nya tidak valid kita bisa juga melakukan exception yakni akan melakukan throw error.

Sebagai contoh:
```php
public function testValidatorValidationException()
    {
        $data = [
            'username' => '',
            'password' => '',
        ];

        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $validator->validate();
            self::fail('ValidationException not thrown');
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }
```
Jadi dengan demikian ketika terjadi error saat validate, error tersebut bisa jadi exception.

## Validation Rules
Laravel memiliki fitur pembuatan rules yang digunakan untuk validasi dengan Validator. Rules - rules tersebut sudah disediakan oleh laravel di dokumentasinya, namun jikalau ada rules yang belum ada bisa dibuat.
Rules yang dibuat bisa multiple artinya bisa lebih dari satu rules. Ini bisa dilakukan dengan menggunakan | sebagai pemisah atau dalam bentuk array.
```php
public function testValidatorMultipleRules()
    {
        $data = [
            'username' => 'test',
            'password' => 'test',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```
Jadi cukup mudah jikalau 1 field memiliki banyak rules.

## Valid Data
Pada proses validasi, secara tidak langsung attribut - attribut yang masuk merupakan attribut yang sudah sesuai oleh rules. Artinya jikalau ada field yang tidak masuk rules maka secara otomatis akan di whitelist.
```php
public function testValidatorValidData()
    {
        $data = [
            'username' => 'user@gmail.com',
            'password' => 'testing',
            'admin' => true,
            'city' => 'a',
            'premium' => true
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        try {
            $valid = $validator->validate();
            Log::info(json_encode($valid, JSON_PRETTY_PRINT));
        } catch (ValidationException $exception) {
            self::assertNotNull($exception->validator);
            $message = $exception->validator->errors();
            Log::error($message->toJson(JSON_PRETTY_PRINT));
        }
    }
```
Setelah validasi sukses maka field - field yang masuk hanya username dan password.

## Validation Message
By default setiap rule validation memiliki message masing-masing. 
Message - message tersebut dapat diubah ke bahasa lain. Untuk melakukan hal itu kita perlu mengubah validation.php di folder lang.
```shell
php artisan lang:publish
```
Kita juga dapat menambahkan custom message untuk attribute di file validation.php
```php
validation.php
...
 'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'username' => [
            'email' => 'we only accept email address for username'
        ]
    ],
```
jadi jika ada field username dan menggunakan rule email , ketika error maka error messagenya akan seperti diatas. Jika custom error tersebut dihapus maka akan kembali ke default.
### Localization
pada folder lang, kita bisa menambahkan bahasa lain misal id. Kemudian kita set localization ke id maka error message nya akan mengikuti lang id.
```php
App::setLocale('id');
$data = [
            'username' => 'test',
            'password' => 'test',
        ];

$rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];
```
maka errornya nanti akan muncul dalam bahasa indonesia.
### Inline
Proses custom message cukup tidak simpel dikarenakan harus mengubah langsung ke file. Pada Validator juga mendukung untuk custom message secara langsung
```php
public function testValidatorInlineMessage()
    {
        $data = [
            'username' => 'test',
            'password' => 'test',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $messages = [
            "required" => ":attribute harus diisi",
            "email" => ":attribute harus email",
            "min" => ":attribute harus minimal :min karakter",
            "max" => ":attribute harus maksimal :max karakter",
        ];

        $validator = Validator::make($data, $rules, $messages);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```

## Additional Validation
Setelah selesai melakukan validation, terkadang ada validasi tambahan yang diperlukan.Hal ini bisa  dilakukan menggunakan callback function 
```php
 public function testValidatorAdditionalValidation()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => 'required|email|max:100',
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $data = $validator->getData();
            if ($data['username'] == $data['password']) {
                $validator->errors()->add('password', 'Password tidak boleh sama dengan username');
            };
        });
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```
Jadi bisa dilihat setelah validasi umum dilakukan, validasi tambahan dilakukan.

## Custom Rules
Ada beberapa case dimana custom rules diperlukan misal untuk mengecek data ke database, dan lain lain. Untuk membuat rules:
```shell
php artisan make:rule <NamaRule>
```
Nanti rules tersebut akan tersimpan di folder \App\Rules
```php
\App\Rules\Uppercase
class Uppercase implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== strtoupper($value)) {
            $fail("The  $attribute must be UPPERCASE");
        }
    }
}
```
Kemudian cara penggunaanya:
```php
public function testValidatorCustomRule()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => ['required', 'email', 'max:100', new Uppercase()], // add custom rules
            'password' => ['required', 'min:6', 'max:20'],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```
Cukup mudah untuk menambahkan validasi secara custom. Namun pada closure tersebut sebenarnya bisa juga memanggil message dari error yang tersedia.
```php
public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== strtoupper($value)) {
            $fail("validation.custom.uppercase")->translate([
                "attribute" => $attribute,
                "value" => $value
            ]);
        }
    }
```
kemudian pada validation.php tambahkan:
```php
validation.php 
... 
'custom.uppercase' => 'The :attribute field with value :value must be UPPERCASE.',
```
Ketika dijalankan maka error message nya akan sesuai dengan yang telah di define di validation.php dengan demikian custom rules dan juga message yang terhubung dengan validation.php bisa dilakukan.

### Multiple Attribute
Jikalau kondisinya adalah rules nya untuk multiple attribute maka bisa dilakukan menggunakan data aware atau implementasi interface DataAwareRule.

Sebagai contoh, 
1. buat rule baru yaitu registrasi
2. kemudian pada RegistrationRule, implement DataAwareRule dan ValidatorAwareRule
```php
class RegistrationRule implements ValidationRule, DataAwareRule, ValidatorAwareRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */

    private array $data;
    private Validator $validator;

    public function setData(array $data): RegistrationRule
    {
        $this->data = $data;
        return $this;
    }

    public function setValidator(Validator $validator): RegistrationRule
    {
        $this->validator = $validator;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $password = $value;
        $username = $this->data['username'];

        if ($password == $username) {
            $fail("$attribute must be different with username");
        }
    }

}
```
Jadi case disini adalah akan dilakukan compare jikalau passwordnya sama dengan username maka validasi error. Mengapa data username bisa terinclude padahal Rule nya hanya diapply di password, karena implementasi data aware dan validator aware yang mengimplementasikan method setData dan setValidator sehingga data username bisa ditangkap.

## Custom Function Rule
Jikalau custom rules dengan class terlalu over, kita bisa langsung membuat custom rule dengan function.
```php
public function testValidatorCustomFunctionRule()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => ['required', 'email', 'max:100', function (string $attribute, string $value, \Closure $fail) {
                if (strtoupper($value) !== $value) {
                    $fail("The field $attribute must be UPPER");
                }
            }],
            'password' => ['required', 'min:6', 'max:20', new RegistrationRule()],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```
dengan demikian custom rule tanpa class bisa dilakukan.

## Rule Class
Laravel juga menyediakan beberapa class rule yang bisa digunakan untuk validator selain rule rule yang sudah ada. Rule tersebut berada di namespace Illuminate\Validation\Rules

Contoh penggunaan:
```php
 public function testValidatorRuleClasses()
    {
        $data = [
            'username' => 'test@gmail.com',
            'password' => 'test@gmail.com',
        ];

        $rules = [
            'username' => ['required', new In(['aldo', 'dono', 'joko'])],
            'password' => ['required', Password::min(6)->letters()->numbers()->symbols()],
        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertFalse($validator->passes());
        self::assertTrue($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```
## Nested Array Validation 
Validasi laravel juga mendukung untuk nested array atau object.

Contoh implementasi:
```php
public function testNestedArray()
    {
        $data = [
            "name" => [
                "firstName" => "John",
                "lastName" => "Doe",
            ],
            "address" => [
                "street" => "bekasi",
                "province" => "jawa barat",
                "country" => "indonesia",
            ]
        ];

        $rules = [
            "name.firstName" => ["required", "max:100"],
            "name.lastName" => ["required", "max:100"],
            "address.street" => ["required", "max:100"],
            "address.province" => ["required", "max:100"],
            "address.country" => ["required", "max:100"],

        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```

### Indexed Array
Jikalau nested array berisikan beberapa data yang bersifat index, maka tidak menggunakan . melainkan *

Contoh implementasi:
```php
public function testNestedIndexArray()
    {
        $data = [
            "name" => [
                "firstName" => "John",
                "lastName" => "Doe",
            ],
            "address" => [
                [
                    "street" => "bekasi",
                    "province" => "jawa barat",
                    "country" => "indonesia",
                ],
                [
                    "street" => "bekasi",
                    "province" => "jawa barat",
                    "country" => "indonesia",
                ],
                [
                    "street" => "bekasi",
                    "province" => "jawa barat",
                    "country" => "indonesia",
                ],
            ]
        ];

        $rules = [
            "name.firstName" => ["required", "max:100"],
            "name.lastName" => ["required", "max:100"],
            "address.*.street" => ["required", "max:100"],
            "address.*.province" => ["required", "max:100"],
            "address.*.country" => ["required", "max:100"],

        ];

        $validator = Validator::make($data, $rules);
        self::assertNotNull($validator);

        self::assertTrue($validator->passes());
        self::assertFalse($validator->fails());

        $message = $validator->getMessageBag();
        Log::info($message->toJson(JSON_PRETTY_PRINT));
    }
```
## HTTP Request Validation
Pada class Request memiliki method yaitu validate. Artinya kita bisa menambahkan rules untuk validasi pada http request class.

Sebagai contoh, akan buat Controller yaitu FormController

Kemudian pada FormController :
```php
\App\Http\Controllers\FormController:: 
public function login(Request $request): Response
    {
        try {
            $rules = [
                "username" => "required",
                "password" => "required"
            ];
            $data = $request->validate($rules);
            return response("OK", Response::HTTP_OK);
        } catch (ValidationException $validationException) {
            return response($validationException->errors(), Response::HTTP_BAD_REQUEST);
        }
    }
```

Pada unit testnya:
```php
public function testLoginSuccess()
    {
        $response = $this->post('/form/login',[
            'username' => 'aldo',
            'password' => 'aldo'
        ]);

        $response->assertStatus(200);
    }

    public function testLoginFail()
    {
        $response = $this->post('/form/login',[
            'username' => '',
            'password' => ''
        ]);

        $response->assertStatus(400);
    }
```

## Error Page
Ada kondisi dimana error message tersebut ingin ditampilkan pada halaman web. Hal ini bisa dilakukan dengan menambahkan variable $error pada blade.
Mengapa hal tersebut bisa terjadi dikarenakan di laravel ada class ShareErrorsFromSession, yakni dimana ketika variable $error tidak null pada view maka akan dishare ke view lainnya.

Sebagai contoh buat view untuk form:
```php
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Form</title>
</head>
<body>
@if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
            <li>{{$error}}</li>
        @endforeach
    </ul>
@endif

<form action="/form" method="post">
    @csrf
    <label>Username: <input type="text" name="username"/></label> <br>
    <label>Password: <input type="password" name="password"/></label> <br>
    <input type="submit" value="Login">
</form>
</body>
</html>
```

Kemudian untuk controller menampilkan form dan submit form:
```php
public function form(): Response
    {
        return response()->view('form');
    }

public function submitForm(Request $request): Response
    {
        $data = $request->validate([
            "username" => "required",
            "password" => "required"
        ]);

        return response("OK", Response::HTTP_OK);
    }
```
Disini tidak perlu try catch dikarenakan ketika terjadi error, laravel langsung menangkap dan akan me-redirect ke halaman sebelumnya dengan membawa error tersebut.

Hasil testnya ketika error:
![img.png](img.png)
Jadi dengan demikian error akan dilempar ke view sebelumnya.

## Error Directive
Sebelumnya kita mengambil error menggunakan $errors, namun bisa juga langsung menggunakan directive @error. Dari directive kita mengambil error by keynya
```php
... 
<form action="/form" method="post">
    @csrf
    <label>Username: @error('username') {{$message}}  @enderror<input type="text" name="username"/></label> <br>
    <label>Password: @error('password') {{$message}}  @enderror<input type="password" name="password"/></label> <br>
    <input type="submit" value="Login">
</form>
```

Maka outputnya:
![img_1.png](img_1.png)

## Repopulating Forms
Pada submit form dan terjadi error, biasanya data sebelumnya akan hilang. Namun kita bisa mengambil data tersebut. Hal ini dikarenakan krn data sebelumya msh tersimpan sementara di laravel session.

```php
... 
<form action="/form" method="post">
    @csrf
    <label>Username: @error('username') {{$message}}  @enderror
        <input type="text" name="username" value="{{old('username')}}"/>
    </label><br>
    <label>Password: @error('password') {{$message}}  @enderror
        <input type="password" name="password" value="{{old('password')}}"/>
    </label><br>
    <input type="submit" value="Login">
</form>
```
![img_2.png](img_2.png)
Namun perlu diingat biasanya data data yang bersifat credentials seperti password, itu dihilangkan

## Custom Request
Jikalau Form Request nya cukup kompleks, better kita buat Class tersendiri untuk Form Request Tersebut. dan validasi nya bisa diintegrasikan dengan Validator

Untuk membuat Form Request sendiri:
```shell
php artisan make:request <NamaFormRequest>
```

Makan akan dibuat class Request sesuai nama request yang dibuat. Pada Class Request tersebut terdapat banyak method seperti:
1. rule () => untuk memberikan rule validasi
2. authorize() => berfungsi untuk menerapkan authorize pada request tersebut (default true jika dihapus)
3. $stopOnFirstFailure => berfungsi untuk memberhentikan validasi jika salah satunya error
4. $redirect / $redirectRoute => untuk redirect
5. after() => untuk validasi lanjutan
6. messages() => mengubah message
7. attributes() => mengubah default nama attribute

Sebagai contoh:
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'email', 'max:100'],
            'password' => ['required', Password::min(6)->letters()->numbers()->symbols()],
        ];
    }
}
```
Kemudian untuk pengggunaanya:
```php
public function submitForm(LoginRequest $request): Response
    {
        $data = $request->validated();
        return response("OK", Response::HTTP_OK);
    }
```

### Before and After Validation
Jika ingin melakukan sesuatu sebelum validasi atau sesudah kita bisa gunakan method:

prepareForValidation()

passedValidation()

```php
... 
protected function prepareForValidation(): void
    {
        $this->merge([
            "username" => strtolower($this->input('username')),
        ]);
    }

protected function passedValidation()
    {
        $this->merge([
            "password" => bcrypt($this->input('password')),
        ]);
    }
```
