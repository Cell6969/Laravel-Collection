# Intro
Laravel memiliki Security Ecosystem.Umumny ada 3 package yang biasa digunakan 
1. Laravel Session(Default)
informasi yang disimpan dalam cookie
2. Laravel Passport (Oauth2 Authentication Provider)
Package yang lumayan kompleks biasa digunakan di web browser, mobile atau API.
3. Laravel Sanctum
Package yang lebih sederhana dibanding Passport. Biasa digunakan untuk SPA.


## User Model
By default, laravel telah menyediakan user model beserta file migrasi. Jalankan file migrasi

Hasil table pada user:
![img.png](img.png)
Sudah otomatis dibuatkan schemanya oleh laravel.

## Laravel Breeze
Laravel breeze adalah fitur sederhana untuk membuat proses authentication secara otomatis. Laravel breeze mendukung halaman registration, login, password reset, email verification dan password confirmation. Dia menngunakan blaze template yang didukung oleh css/tailwind.

Untuk menambahkan laravel-breeze:
```shell
composer require laravel/breeze=v1.26.2 --dev
```

kemudian instalasi laravel-breeze:
```shell
php artisan breeze:install
```

Pilih Blade with Alpine, dark mode (opsional), dan phpunit.
Setelah selesai nanti semua akan digenerate banyak file termasuk routes

Jalankan php artisan serve, nanti akan muncul tampilan untuk registrasi dan login. Coba registrasi maka nanti akunnya akan terdaftar.
![img_1.png](img_1.png)

Jadi cukup bagus menggunakan laravel-breeze. Sudah tergenerate dari controller, resource, route, hingga ke view.

## Authentication
Proses authentication tidak menggunakan User Model melainkan menggunakan Facade Auth.

Buat seeder untuk user:
```php
 public function run(): void
    {
        User::query()->create([
            "name" => "aldo",
            "email" => "aldo@gmail.com",
            "password" => Hash::make('aldo123')
        ]);
    }
```

Kemudian pada testnya:
```php
public function testAuthentication()
    {
        $this->seed([
            UserSeeder::class,
        ]);

        $success = Auth::attempt([
            "email" => "aldo@gmail.com",
            "password" => "aldo123"
        ], true);

        self::assertTrue($success);

        $user = Auth::user();
        self::assertNotNull($user);
        Log::info(json_encode($user, JSON_PRETTY_PRINT));
    }

    public function testGuest()
    {
        $user = Auth::user();
        self::assertNull($user);
    }
```
Jadi untuk melakukan login, kemudian mendapatkan data user yang sudah login cukup menggunakan facade Auth::

## User Session
saat menggunakan Auth::login(), otomatis data user akan disimpan di session. Bisa juga generate session agar informasi user disimpan di cookie. Saat memanggil Auth::attempt(), jika berhasil maka secara otomatis Auth::login() juga akan dipanggil.

Contoh implementasi, buat UserController
```php
\App\Http\Controllers\UserController::class 
public function login(Request $request)
    {
        $response = Auth::attempt([
            "email" => $request->query("email", "wrong"),
            "password" => $request->query("password", "wrong")
        ], true);

        if ($response) {
            Session::regenerate();
            return redirect("/users/current");
        } else {
            return "Invalid Credentials";
        }
    }

public function current()
    {
        $user = Auth::user();
        if ($user) {
            return "Hello $user->name";
        } else {
            return "Hello Guest";
        }
    }
```

registrasikan pada web.php
```php
Route::get('/users/login', [\App\Http\Controllers\UserController::class, 'login']);
Route::get('/users/current', [\App\Http\Controllers\UserController::class, 'current']);
```

jalankan php serve,

- jika di cek pada /users/current:
![img_2.png](img_2.png)

Hal ini terjadi karena kita belum login oleh karena itu session belum mendapatkan data kita. Jika kita login: maka 
![img_3.png](img_3.png)

karena kita telah login maka session sudah menyimpan data kita dan bisa diakses.

Lalu untuk unit test nya:
```php
public function testLogin()
    {
        $this->seed([UserSeeder::class]);

        $this->get("/users/login?email=aldo@gmail.com&password=aldo123")
            ->assertStatus(302)
            ->assertRedirect("/users/current");
    }

public function testCurrent()
    {
        $this->seed([UserSeeder::class]);

        // test if user not authenticated
        $this->get("/users/current")
            ->assertSeeText("Hello Guest");

        $user = User::query()->where("email", "=", "aldo@gmail.com")->first();

        $this->actingAs($user)
            ->get("/users/current")
            ->assertSeeText("Hello aldo");
    }
```
Jadi simpelnya laravel menggunakan facade Auth yang dimana jika user berhasil login maka data tersebut akan disimpan dalam session.

## Hash Facade
