# Dependency Injection

Dependency injection merupakan suatu teknik dimana sebuah object menerima object lain yang dibutuhkan  (dependencies).

Sebagai contoh , buat file **Foo.php** pada */app/Data*. 

Foo.php
```php
<?php

namespace App\Data;

class Foo
{
    public function foo(): string
    {
        return "foo";
    }
}
```

Kemudian buat class Bar

Bar.php
```php
<?php

namespace App\Data;

class Bar
{
    private Foo $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }

    public function bar(): string
    {
        return $this->foo->foo() . ' and Bar';
    }
}
```

Pada code Bar, terlihat bahwa class Foo di inject pada class Bar melalui constructor. Kemudian pada method bar() outputnya adalah memanggil method yang ada pada Class Foo.

Cara penggunaan:
```php
<?php

namespace Tests\Feature;

use App\Data\Bar;
use App\Data\Foo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DependencyInjectionTest extends TestCase
{
    public function testDependencyInjection()
    {
        $foo = new Foo();
        $bar = new Bar($foo);

        self::assertEquals('Foo and Bar', $bar->bar());
    }
}
```
