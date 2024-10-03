# Method 

Terlebih dahulu buat unit test

## Insert
Untuk melakukan insert pada database bisa dilakukan dengan cara berikut:
```php
public function testInsert()
    {
        $category = new Category();
        $category->id = "GADGET";
        $category->name = "Gadget";
        $result = $category->save();

        self::assertTrue($result);
    }
```

## Insert Many
Untuk melakukan insert dengan banyak data, maka bisa dilakukan dengan menggunakan orm.

Contoh implementasi:
```php
public function testInsertMany()
    {
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                "id" => $i,
                "name" => "Name $i",
            ];
        }

        $result = Category::query()->insert($categories);

        self::assertTrue($result);

        $total = Category::query()->count();

        assertEquals(10, $total);
    }
```

## Find
Laravel menyediakan method dengan prefix find() di query builder untuk mendapatkan satu data menggunakan primary key.

Sebagai contoh, untuk seeder akan diupdate:
```php
<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->save();
    }
}
```
Kemudian untuk unit test nya:
```php
public function testFind()
    {
        $this->seed(CategorySeeder::class);
        $category = Category::query()->find("FOOD");
        self::assertNotNull($category);
        self::assertEquals("FOOD", $category->id);
        self::assertEquals("Food", $category->name);
        self::assertEquals("Food Category", $category->description);
    }
```
## Update
Untuk melakukan update maka bisa menggunakan method update atau save. Saat melakukan update, pertama yaitu melakukan find terlebih dahulu kemudian save. Oleh karena itu tidak perlu membuat object baru.

Contoh implementasi:
```php
public function testUpdate()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->find("FOOD");
        $category->name = "Food Updated";

        $result = $category->update();
        self::assertTrue($result);
    }
```

## Select
Jiakalau ingin mengambil banyak data , maka bisa menggunakan select.

Contoh implementasi:
```php
public function testSelect()
    {
        // insert data
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->id = "ID $i";
            $category->name = "Name $i";
            $category->save();
        }

        $categories = Category::query()->get();
        self::assertEquals(5, $categories->count());
        $categories->each(function (Category $category) {
           self::assertNull($category->description);
        });
    }
```
Pada saat get (select), result yang disimpan merupakan collection array, bukan collection model. Oleh karena itu bisa juga langsung melakukan update
```php
$categories->each(function ($category) {
           self::assertNull($category->description);

           $category->description = "Updated";
           $category->update();
        });
```

## Update Many
Jikalau ingin mengupdate data banyak maka bisa dilakukan dengan query builder.

Contoh implementasi:
```php
public function testUpdateMany()
    {
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                "id" => $i,
                "name" => "Name $i",
            ];
        }

        $result = Category::query()->insert($categories);
        self::assertTrue($result);

        // Update data
        Category::query()->whereNull('description')->update([
            "description" => "Updated",
        ]);
        $total = Category::query()->where("description", "=", "Updated")->count();
        assertEquals(10, $total);
    }

```
## Delete
Ketika ingin delete maka seperti update, harus menemukan data nya terlebih dahulu. Disarankan tidak mengguankan new object lagi.

Contoh implementasi:
```php
public function testDelete()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->find("FOOD");
        $result = $category->delete();
        assertTrue($result);

        $total = Category::query()->count();
        self::assertEquals(0, $total);
    }
```

## Delete Many
contoh implementasi:
```php
public function testDeleteMany()
    {
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                "id" => "$i",
                "name" => "Name $i",
            ];
        }
//        Insert data
        $result = Category::query()->insert($categories);
        self::assertTrue($result);

//        Count Data before delete
        $total = Category::query()->count();
        self::assertEquals(10, $total);

//        Delete data
        Category::query()->whereNull('description')->delete();

//        Count data after delete
        $total = Category::query()->count();
        self::assertEquals(0, $total);
    }
```

## UUID
Laravel eloquent memiliki fitur untuk mengenerate UUID untuk primary key. UUID pada laravel eloquent sudah berurut jadi tidak perlu khawatir akan random.

Contoh implementasi,
buat model Voucher:
```shell
php artisan make:model Voucher --migration --seed
```

Kemudain update Voucher migration:
```php
public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->primary();
            $table->string('name', 100)->nullable(false);
            $table->string('voucher_code', 200)->nullable(false);
            $table->timestamp("created_at")->nullable(false)->useCurrent();
        });
    }
```
jalankan migration:
```shell
php artisan migrate
```

Kemudian buat model Voucher pada App\Model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasUuids;

    protected $table = "vouchers";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    public $timestamps = false;
}
```
Kemudian untuk unit test nya:
```php
 public function testCreateVoucher()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->voucher_code = "12345";
        $voucher->save();
        assertNotNull($voucher->id);
    }
```
Dengan demikian data yang diinsert secara otomatis akan tergenerate UUID tersebut.

Selain untuk primary key, UUID juga bisa digenerate untuk field lain. Hal ini bisa dilakukan dengan mengoverride method uniqueIds() pada HasUUids;

Contoh :
```php
class Voucher extends Model
{
    use HasUuids;

    protected $table = "vouchers";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    public $timestamps = false;

    public function uniqueIds(): array
    {
        return [$this->primaryKey, "voucher_code"];
    }
}
```

Kemudian untuk testnya:
```php
public function testCreateVoucherUUID()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->save();

        self::assertNotNull($voucher->id);
        self::assertNotNull($voucher->voucher_code);
    }
```
