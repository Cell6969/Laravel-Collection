# Database Transaction

Untuk melakukan transaction bisa menggunakan DB::transaction(function). Contoh implementasi
```php
public function testTransactionSuccess()
    {
        DB::transaction(function () {
            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "GADGET",
                "name" => "Gadget",
                "description" => "Gadget Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);

            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "FOOD",
                "name" => "Food",
                "description" => "Food Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);
        });

        $result = DB::select("select * from categories");
        self::assertCount(2, $result);
    }
```

Untuk case yang gagal:
```php
public function testTransactionFailed()
    {
        try {
            DB::transaction(function () {
                DB::insert('insert into categories(id, name, description, created_at) 
                values (:id, :name,:description, :created_at)', [
                    "id" => "GADGET",
                    "name" => "Gadget",
                    "description" => "Gadget Category",
                    "created_at" => "2020-10-10 10:10:10"
                ]);
    
                DB::insert('insert into categories(id, name, description, created_at) 
                values (:id, :name,:description, :created_at)', [
                    "id" => "GADGET",
                    "name" => "Gadget",
                    "description" => "Gadget Category",
                    "created_at" => "2020-10-10 10:10:10"
                ]);
            });
        } catch (QueryException $error){

        }

        $result = DB::select("select * from categories");
        self::assertCount(0, $result);
    }
```

Pada code diatas akan terjadi error, karena data yang dimasukkan sama (duplicate) maka tidak akan terkeksekusi semuanya. Untuk manual transaction bisa seperti ini
```php
public function testManualTransaction()
    {
        try {
            //code...
            DB::beginTransaction();

            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "GADGET",
                "name" => "Gadget",
                "description" => "Gadget Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);

            DB::insert('insert into categories(id, name, description, created_at) 
            values (:id, :name,:description, :created_at)', [
                "id" => "FOOD",
                "name" => "Food",
                "description" => "Food Category",
                "created_at" => "2020-10-10 10:10:10"
            ]);
            
            DB::commit();
        } catch (QueryException $th) {
            //throw $th;
            Log::error($th);
        }

        $result = DB::select("select * from categories");
        self::assertCount(2, $result);
    }
```