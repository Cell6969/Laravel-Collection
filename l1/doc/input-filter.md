# Input Filter

Pada laravel, input dapat difilter sesuai kebutuhan , misal kita ingin mengambil key tertentu atau meng exclude key tertentu.

Contoh penggunaan:

```php
public function filterOnly(Request $request): string
    {
        $name = $request->only('name.first', 'name.last');
        return json_encode($name);
    }

    public function filterExcept(Request $request): string
    {
        $user = $request->except('admin');
        return json_encode($user);
    }
```

Untuk unit testnya:

```php
public function testFilterOnly()
    {
        $this->post('/input/filter/only', [
            "name" => [
                "first" => "jonathan",
                "last" => "donovan",
                "middle" => "arga"
            ],
            "phone" => "0892222"
        ])->assertSeeText("jonathan")->assertSeeText("donovan")
            ->assertDontSeeText("arga")->assertDontSeeText("0892222");
    }

    public function testFilterExcept()
    {
        $this->post('/input/filter/except', [
            'username' => "arga",
            'password' => "12345",
            "admin" => "true"
        ])->assertSeeText("arga")->assertSeeText("12345")->assertDontSeeText('admin');
    }
```

## Merge Input

Merge input biasanya digunakan ketika kita ingin mereplace value dengan default yang kita tentukan. Untuk metode nya bisa menggunakan merge atau mergeIf. Merge jika langsung mereplace sedangkan mergeIf jika ada yang kosong.

Contoh

```php
public function filterMerge(Request $request): string
    {
        $request->merge([
            'admin' => false
        ]);
        $user = $request->input();
        return json_encode($user);
    }
```

Lalu pada unit test:
```php
public function testFilterMerge()
    {
        $this->post('/input/filter/merge', [
            'username' => 'a',
            'password' => '123',
            'admin' => 'true'
        ])->assertSeeText('a')->assertSeeText('123')
            ->assertSeeText('admin')->assertSeeText('false');
    }
```
