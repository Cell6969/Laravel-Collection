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

Case unutuk null dan not null:
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

