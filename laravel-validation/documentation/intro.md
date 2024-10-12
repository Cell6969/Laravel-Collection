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
