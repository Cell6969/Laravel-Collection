# Collection

## Membuat Collection
```php
public function testCreateCollection() 
{
        $collection = collect([1,2,3]);

        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());
}
```

## For Each
```php
public function testForEach()
    {
        $collection = collect([1,2,3,4,5,6,7,8,9]);

        foreach ($collection as $key => $value) {
            $this->assertEquals($key+1, $value);
        }
    }
```

## Manipulasi Collection
![alt text](image.png)

Contoh penggunaan:
```php
public function testCrud()
    {
        // push 
        $collection = collect([]);git 
        $collection->push(1,2,3);
        $this->assertEqualsCanonicalizing([1,2,3], $collection->all());

        // pop
        $result = $collection->pop();

        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1,2], $collection->all());
    }
```

## Mapping
Mapping adalah transformasi (mengubah bentuk data) menjadi data lain. Mapping membutuhkan function sebagai parameter yang digunakan untuk membentuk data lainnya. Urutan Collection hasil mapping sesuai dengan urutan collection aslinya.
![alt text](image-1.png)

Contoh:
```php
public function testMap()
    {
        $collection = collect([1,2,3]);
        $result = $collection->map(function($item){
            return $item * 2; // kali 2
        });
        $this->assertEqualsCanonicalizing([2,4,6], $result->all());
    }
```

### Map Into
buat object Person
```php
<?php

namespace App\Data;

class Person
{
    var string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

```

Kemudian pada penggunaanya:
```php
public function testMapInto()
    {
        $collection = collect(["jonathan"]);
        $result = $collection->mapInto(Person::class);

        $this->assertEquals([new Person("jonathan")], $result->all());
    }
```

### Map Spread
Ada kondisi dimana pada array terdapat 2 element, sehingga untuk memecah 2 element tersebut menjadi parameter maka digunakan Map Spread. Contoh:
```php
public function testMapSpread()
    {
        $collection = collect([
            ["dono", "van"],
            ["jono", "joni"]
        ]);

        $result = $collection->mapSpread(function ($firstname, $lastname) {
            $fullname = $firstname . ' ' . $lastname;
            return new Person($fullname);
        });

        $this->assertEquals([
            new Person("dono van"),
            new Person("jono joni"),
        ], $result->all());
    }
```

### Map to Groups
Untuk mapping array berdasarkan group bisa juga menggunakan mapToGroup
```php
public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "jonathan",
                "department" => "IT"
            ],
            [
                "name" => "dodi",
                "department" => "IT"
            ],
            [
                "name" => "dono",
                "department" => "Marketing"
            ],
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["department"] => $person["name"]
            ];
        });

        $this->assertEquals([
            "IT" => collect(["jonathan", "dodi"]),
            "Marketing" => collect(["dono"])
        ], $result->all());
    }
```