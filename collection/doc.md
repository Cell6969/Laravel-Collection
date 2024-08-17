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

