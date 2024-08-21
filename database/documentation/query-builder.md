# Query Builder

## Insert

Contoh implementasi
```php
public function testInsert()
    {
        DB::table('categories')->insert([
            'id' => "GADGET",
            "name" => "Gadget"
        ]);

        DB::table('categories')->insert([
            'id' => "FOOD",
            "name" => "Food"
        ]);

        $result = DB::select('select count(id) as total from categories');
        self::assertEquals(2, $result[0]->total);
    }
```

## Select
```php
public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table('categories')->select(['id', 'name'])->get();
        self::assertNotNull($collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```
## Where
Buat fungsi untuk insertnya:
```php
public function insertCategories()
    {
        DB::table('categories')->insert([
            "id" => "SMARTPHONE",
            "name" => "Smartphone",
            "created_ait" => "2024-08-18 10:10:10"
        ]);

        DB::table('categories')->insert([
            "id" => "FOOD",
            "name" => "Food",
            "created_ait" => "2024-08-18 10:10:10"
        ]);

        DB::table('categories')->insert([
            "id" => "LAPTOP",
            "name" => "Laptop",
            "created_ait" => "2024-08-18 10:10:10"
        ]);

        DB::table('categories')->insert([
            "id" => "FASHION",
            "name" => "Fashion",
            "created_ait" => "2024-08-18 10:10:10"
        ]);
    }
```

beberapa method where:
![alt text](image-2.png)

```php
 public function testWhere()
    {
        $this->insertCategories();

        $collection = DB::table("categories")->where(function (Builder $builder){
            $builder->where('id', '=', 'SMARTPHONE');
            $builder->orWhere('id', '=', 'LAPTOP');
        })->get();

        self::assertCount(2, $collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```

Contoh case lain dengan between 
```php
public function testWhereBetweenMethod()
    {
        $this->insertCategories();

        $collection = DB::table('categories')
            ->whereBetween('created_at', ['2022-08-18 10:10:10', '2024-08-18 10:10:10'])
            ->get();

        self::assertCount(4, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```

Case dengan In:
```php
public function testWhereIn()
    {
        $this->insertCategories();

        $collection = DB::table('categories')->whereIn('id', ['SMARTPHONE', "LAPTOP"])->get();

        self::assertCount(2, $collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```

Case untuk null dan not null:
```php
public function testWhereNull()
    {
        $this->insertCategories();

        $collection = DB::table('categories')->whereNull('description')->get();

        self::assertCount(4, $collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });

    }
```

Case untuk cek waktu:
```php
 public function testWhereDate()
    {
        $this->insertCategories();

        $collection = DB::table('categories')
            ->whereDate("created_at", "2024-08-18")->get();

        self::assertCount(3, $collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```
## Update
contoh implementasinya:
```php
public function testUpdate()
    {
        $this->insertCategories();

        DB::table('categories')->where('id', '=', 'SMARTPHONE')
            ->update([
                "name" => "Handphone"
            ]);

        $collection = DB::table('categories')->where('name', '=', 'Handphone')
            ->get();
        

        self::assertCount(1, $collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```

Selain itu ada juga Upsert (Update or Insert) yakni dimana ketika data yang diupdate tidak ada maka akan membuat data baru
```php
public function testUpsert()
    {
        DB::table('categories')->updateOrInsert([
            'id' => 'VOUCHER'
        ], [
            "name" => "Voucher",
            "description" => "Ticket and Voucher",
            "created_at" => "2022-08-18 10:10:10"
        ]);

        $collection = DB::table('categories')->where('id', '=', 'VOUCHER')->get();
        self::assertCount(1, $collection);
        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```
Selain itu bisa juga update increment maupun decrement di laravel. Sebagai contoh buat table baru bernama counters (query ada pada doc).
Contoh implementasinya:
```php
public function testIncrement()
    {
        DB::table('counters')->where('id', '=', 'sample')->increment('counter', 1);

        $collection = DB::table('counters')->where('id', '=', 'sample')->get();
        self::assertCount(1, $collection);

        $collection->each(function($item){
            Log::info(json_encode($item));
        });
    }
```

