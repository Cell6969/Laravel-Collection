# Facades

Facades pada laravel adalah class yang menyediakan static akses ke fitur di Service Container. Penggunaan facades dilakukan seperlunya saja dikarenakan kompleksnya parameter yang ada pada constructor. Sebelumnya, kita bisa menggunakan helper, namun didalam helper menggunakan Facades. Sebagai contoh buat unit test FacadesTest

FacadeTest

```php
 public function testConfig()
    {
        // menggunakan helper
        $firstname1 = config('contoh.name.firstname');

        // menggunakan facades
        $firstname2 = Config::get('contoh.name.firstname');

        self::assertEquals($firstname1, $firstname2);
    }
```

Untuk akses langsung dependency nya tanpa menggunakan helper, bisa seperti ini:

```php
public function testConfigDependency()
    {
        $config = $this->app->make('config');
        $firstname1 = config('contoh.name.firstname');
        $firstname2 = $config->get('contoh.name.firstname');

        self::assertEquals($firstname1, $firstname2);
    }
```

## Facades Mock

Beberapa kasus memang perlu digunakan facades dibanding helper. Sebagai contoh adalah mock. Pada laravel disediakan function untuk melakukan mocking di Facades, untuk mempermudah implementasi unit test.

Contoh:

```php
public function testFacadedMock()
    {
        Config::shouldReceive('get')
            ->with('contoh.name.firstname')
            ->andReturn('jonathan');

        $firstname = Config::get('contoh.name.firstname');

        self::assertEquals('jonathan', $firstname);
    }
```
