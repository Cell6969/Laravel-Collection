# Attributes

## Timestamps
Ketika attribute timestamp dijadikan sebagai true, maka secara otomatis eloquent akan membuat attribute created_at dan updated_at. Di migration, kita bisa membuat hal itu secara otomatis.

Contoh implementasi:

1. Buat model Comment
```shell
php artisan make:model Comment --migration --seed
```
2. Update migration Comment
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('email', 100)->nullable(false);
            $table->string('title', 200)->nullable(false);
            $table->text('comment')->nullable(true);
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
```
Jalankan migrasi
3. Edit model Comment
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = "id";
    protected $keyType = "integer";

    public $incrementing = true;
    public $timestamps = true; // default true
}
```
4. Unit testnya:
```php
public function testCreateComment()
    {
        $comment = new Comment();
        $comment->email = "alo@gmail.com";
        $comment->title = "Sample title";
        $comment->comment = "Ini comment";

        $comment->save();

        self::assertNotNull($comment->id);
    }
```

## Default Attributes Values
Laravel model memiliki fitur default attributes values, dimana kita bisa membuat default value untuk attributes di Model. Sehingga ketika pertama kali dibuat object modelnya, default valuenya akan mengikuti.

Contoh implementasi:

Ubah bagian pada model Comment:
```php
class Comment extends Model
{
    protected $table = 'comments';
    protected $primaryKey = "id";
    protected $keyType = "integer";

    public $incrementing = true;
    public $timestamps = true; // default true

    protected $attributes = [
        "title" => "Default title",
        "comment" => "Default Comment"
    ];
}
```
Kemudian pada unit testnya:
```php
public function testDefaultAttributes()
    {
        $comment = new Comment();
        $comment->email = "alo@gmail.com";

        $comment->save();

        self::assertNotNull($comment->id);
        self::assertNotNull($comment->title);
        self::assertNotNull($comment->comment);
    }
```
Dengan demikian value pada title comment akan terisi secara default ketika data tersebut kosong pada saat melakukan insert.

## Fillable Attributes
Laravel memiliki fitur untuk membuat model secara otomatis menggunakan method create. Hal ini sangat berguna semisal data yang dimasukan terlalu banyak field.
Namun sebelum prakteknya terlebih dahulu harus melakukan setup pada model

Category.model
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @Property string id
 * @Property string $name
 * @Property string $description
 */
class Category extends Model
{
    protected $table = "categories";
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        "id",
        "name",
        "description",
    ];
}
```
Tujuan dari fillable adalah untuk memberi tahu laravel bahwa atrribute attribute tersebut bisa langsung direct terisi.

Pada unit testnya:
```php
 public function testCreate()
    {
        $request = [
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Food Category",
        ];

        $category = new Category($request);
        $category->save();

        self::assertNotNull($category->id);
    }
```
Dengan demikian sangat mempermudah untuk proses create atau update data.

Selain cara tersebut, bisa juga langsung menggunakan query builder
```php
public function testCreateUsingQueryBuilder()
    {
        $request = [
            "id" => "FOOD",
            "name" => "Food",
            "description" => "Food Category",
        ];

        $category = Category::query()->create($request);
        $category->save();
        self::assertNotNull($category->id);
    }
```

Untuk kasus update, menggunakan fill kemudian save

Contoh implementasi:
```php
public function testUpdateMass()
    {
        $this->seed(CategorySeeder::class);

        $request = [
          "name" => "Food Updated",
          "description" => "Food Category Updated",
        ];

        $category = Category::query()->find("FOOD");
        $category->fill($request);
        $category->save();

        self::assertNotNull($category->id);
    }
```
## Soft Delete
Soft Delete merupakan konsep dimana kita menghapus data namun data tersebut hanya di flag,tidak benar benar dihapus.
Untuk mengimplementasikan soft delete, bisa menggunakan trait SoftDeletes

Contoh implementasi, pada table voucher ditambahkan attribut soft delete
```shell
php artisan make:migration add_delete_at_column_to_vouchers
```
Tambahkan, attribute soft delete
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
```
Kemudian jalankan migrate.

Tambahkan trait pada model voucher
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasUuids,SoftDeletes; // add softdeletes

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

Pada unit test:
```php
public function testSoftDelete()
    {
        $this->seed(VoucherSeeder::class);
        $voucher = Voucher::query()->where('name', '=', 'Sample Voucher')->first();
        $voucher->delete();

        $voucher = Voucher::query()->where('name', '=', 'Sample Voucher')->first();
        assertNull($voucher);
    }
```

Pada database data tersebut akan tetap ada tetapi memiliki flag deleted_at. Ketika select secara otomatis akan melakukan where not null untuk kolom deleted_at

Untuk mengambil semua data termasuk yang terkena softdelete bisa seperti berikut:
```php
  // for displaying data affected with soft delete
        $voucher = Voucher::withTrashed()->where('name', '=', 'Sample Voucher')->first();
        self::assertNotNull($voucher);
```

Kemudian untuk mendelete data secara hard  delete:
```php
// for forcely delete data
        $voucher->forceDelete();
        $voucher = Voucher::query()->where('name', '=', 'Sample Voucher')->first();
        self::assertNull($voucher);
```
