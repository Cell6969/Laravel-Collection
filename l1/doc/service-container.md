# Service Container

Service Container direpresentasikan dalam class bernama Application (App). Bisa dikatakan Application ini sebagai manager untuk class - class yang dibuat. Dengan menggunakan Service Container, kita tidak perlu membuat object secara manual menggunakan _new_.

Sebagai contoh

```php
public function testDependencyInjection()
    {
        $foo = $this->app->make(Foo::class);
        $foo2 = $this->app->make(Foo::class);

        self::assertEquals("Foo", $foo->foo());
        self::assertEquals("Foo", $foo2->foo());
        self::assertNotSame($foo, $foo2);
    }
```

Pada code diatas, class foo dibuat menggunakan app. Namun diperhatikan bahwa ketika foo2 maka akan membuat class Foo sehingga tidak sama antara $foo dan $foo2.

## Bind & Closure

Pada banyak kasus, ketika memanggil sebuah object, object tersebut memiliki parameter constructor. Jika langsung memakai cara **app->make** maka langsung error dikarenakan laravel tidak mengetahui cara penggunaanya.

Contoh, buat Class Person.php

```php
<?php

namespace App\Data;

class Person
{
    public function __construct(
        public string $firstname,
        public string $lastname
    ) {
    }
}
```

Class Person memiliki parameter constructor sehingga tidak bisa langsung membuat object. Untuk menghandle nya bisa dilakukan sebagai berikut:

```php
public function testBind()
    {
        $this->app->bind(Person::class, function ($app) {
            return new Person("jonathan", "doe");
        });

        $person1 = $this->app->make(Person::class);
        $person2 = $this->app->make(Person::class);

        self::assertEquals('jonathan', $person1->firstname);
        self::assertEquals('jonathan', $person2->firstname);
        self::assertNotSame($person1, $person2);
    }
```

Pada code diatas, binding dilakukan untuk memanggil class Person.

## Singleton

Pada contoh - contoh pemanggilan class sebelumnya, class akan di bentuk berulang kali dengan membuat class baru. Hal ini bisa jadi konsumsi memori yang cukup berat. Solusinya adalah membuat object menjadi singleton sehingga pada pemanggilan tidak perlu berulang - ulang membuat.

Contoh

```php
public function testSingleton()
    {
        $this->app->singleton(Person::class, function($app){
            return new Person("jonathan", "doe");
        });

        $person1 = $this->app->make(Person::class);
        $person2 = $this->app->make(Person::class);

        self::assertEquals('jonathan', $person1->firstname);
        self::assertEquals('jonathan', $person2->firstname);
        self::assertSame($person1, $person2);
    }
```

## Instance

Selain menggunakan singleton, bisa juga membuat object yang sudah ada menjadi singleton menggunakan fungsi **instance**.

Contoh,

```php
public function testInstance()
    {
        $person = new Person("jonathan", "doe");
        $this->app->instance(Person::class, $person);

        $person1 = $this->app->make(Person::class);
        $person2 = $this->app->make(Person::class);

        self::assertEquals('jonathan', $person1->firstname);
        self::assertEquals('jonathan', $person2->firstname);
        self::assertSame($person1, $person2);
    }
```

## Dependency Injection

Banyaknya kasus, beberapa class memiliki dependency dengan class lain. Pada kasus sebelumnya kita harus membuat new dan memasukkan dependency nya sebagai parameter pada class yang ingin dibuat. Namun service container laravel bisa dilakukan tanpa melakukan hal tersebut.

```php
public function testDependencyInjection()
    {
        // contoh 1: Tanpa membuat singleton
        // $foo = $this->app->make(Foo::class);

        // contoh 2: Dengan singleton
        $this->app->singleton(Foo::class, function ($app) {
            return new Foo();
        });

        $foo = $this->app->make(Foo::class);
        $bar1 = $this->app->make(Bar::class);
        $bar2 = $this->app->make(Bar::class);

        self::assertSame($foo, $bar1->foo);
        self::assertNotSame($bar1, $bar2);
    }
```

Pada code diatas, object Bar dan Foo dipanggil oleh app namun pada bar tidak dimasukkan parameter $foo. Hal ini dikarenakan laravel otomatis sudah mengenali dependency object melalui constructor Bar.

Namun perlu diperhatikan, object bar yang dibuat tidak singleton sehingga antara $bar1 dan $bar2 merupakan object berbeda walaupun Foo yang sama. Untuk $bar selalu object yang sama

```php
public function testDependencyInjection()
    {
        // contoh 1: Tanpa membuat singleton
        // $foo = $this->app->make(Foo::class);

        // contoh 2: Dengan singleton
        $this->app->singleton(Foo::class, function ($app) {
            return new Foo();
        });

        $this->app->singleton(Bar::class, function($app){
            $foo = $app->make(Foo::class);
            return new Bar($foo);
        });

        $foo = $this->app->make(Foo::class);
        $bar1 = $this->app->make(Bar::class);
        $bar2 = $this->app->make(Bar::class);

        self::assertSame($foo, $bar1->foo);
        self::assertSame($bar1, $bar2);
    }
```

## Binding Interface

Selain class, interface yang berupa kontrak dapat dibind ketika ingin membuat class.

Sebagai contoh, buat interface **HelloService.php** pada _/app/Services_.
HelloService.php

```php
<?php

namespace App\Services;

interface HelloService
{
    public function hello(string $name): string;
}
```

Lalu buat HelloServiceIndonesia untuk implement interface
HelloServiceIndonesia.php

```php
<?php

namespace App\Services;

class HelloServiceIndonesia implements HelloService
{
    public function hello(string $name): string
    {
        return "Hello $name";
    }
}
```

Kemudian untuk pemanggilannya:

```php
public function testInterfacetoClass()
    {
        $this->app->singleton(HelloService::class, function($app){
            return new HelloServiceIndonesia();
        });

        $helloService = $this->app->make(HelloService::class);
        self::assertEquals('Hello jon', $helloService->hello('jon'));
    }
```
