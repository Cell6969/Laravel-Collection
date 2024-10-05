# Query

## Query Scope
Pada kasus softdelete, ketika melakukan query select secara otomatis dia akan melakukan condition where null pada deleted_at. Hal ini dikarenakan softdelete menerapkan query scope pada implementasinya. Jikalau kita ingin membuat sendiri conditional tersebut, bisa juga dilakukan menggunakan query scope.

Query scope ada yaitu Global Scope dan Local Scope.

## Global Scope
Query Global Scope merupakan kondisi query yang bisa kita tambahkan secara default ke Model

Untuk contoh implementasi, pada model Category kita tambahkan isActive (boolean). Jadi ketika kita membaca data Category kita secara otomatis akan mengambil data yang isActive = true.

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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_active')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
```
Sebelum menjalankan migrasi, terlebih dahulu membuat scope
```shell
php artisan make:scope IsActiveScope
```
Akan terbuat file Scope pada model. Edit menjadi:
```php
<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class IsActiveScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('is_active', '=', true);
    }
}
```
Lalu edit model Category tambahkan scope:
```php
<?php

namespace App\Models;

use App\Models\Scopes\IsActiveScope;
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

    // tambahkan scope
    protected static function boot():void
    {
        parent::boot();
        self::addGlobalScope(new IsActiveScope());
    }
}
```
Lalu pada unit test:
```php
 public function testGlobalScope()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = false;
        $category->save();

        $category = Category::query()->find("FOOD");
        self::assertNull($category);
    }
```
Maka secara otomatis, query yang berjalan akan melalukan condition where is_active = ?.

Ada case dimana kita tidak memerlukan global scope. Hal ini bisa dilakukan dengan me-disable global soft.

Contoh implementasi:
```php
public function testGlobalScope()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = false;
        $category->save();

        $category = Category::query()->find("FOOD");
        self::assertNull($category);

        // Add withoutGlobalScope to remove globalscope
        $category = Category::query()->withoutGlobalScopes([IsActiveScope::class])->get();
        self::assertNotNull($category);
    }
```
Jadi mirip dengan softdelete ada tambahan menggunakan withoutGlobalScopes lalu masukkan scope yang tidak diinginkan.

## LocalScope
Perbedaanya dengan global scope adalah, dia by default tidak langsung aktif kecuali dari query. Localscope sendiri juga harus eksplisit berbeda dengan globalscope.

Untuk contoh implementasi, tambahkan kolom is_active pada vouchers
1. Buat migration is_active vouchers
```shell
 php artisan make:migration add_is_active_column_to_vouchers
```
2. Tambahkan is_active column pada vouchers:
```shell
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
            $table->boolean('is_active')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
```
3. tambahkan scope isActive dan nonActive pada model voucher
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

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

    // add local scope
    public function scopeActive(Builder $builder):void
    {
        $builder->where('is_active', '=', true);
    }

    public function scopeNonActive(Builder $builder):void
    {
        $builder->where('is_active', '=', false);
    }
}
```
4. Jalankan migrasi
5. Lalu penggunaany
```php
public function testLocalScope()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->is_active = true;
        $voucher->save();

        $total = Voucher::query()->active()->count();
        self::assertEquals(1, $total);

        $total = Voucher::query()->nonActive()->count();
        self::assertEquals(0, $total);
    }
```

# Relationships
Laravel eloquent juga mendukung untuk relationship antar table mulai dari one to one, one to many dan many to many

## One To One
Untuk kasus one to one, akan implement 2 model yaitu Customer dan Wallet.

1. Generate model Customer dan Wallet
```shell
php artisan make:model Customer --migration --seed

php artisan make:model Wallet --migration --seed
```

2. Define schema customer dan model
Customer
```php
public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string("id", 100)->nullable(false)->primary();
            $table->string("name", 100)->nullable(false);
            $table->string("email", 100)->nullable(false)->unique("customer_email");
        });
    }
```

Wallet
```php
public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('customer_id', 100)->nullable(false);
            $table->bigInteger("amount")->nullable(false)->default(0);
            $table->foreign("customer_id")->on("customers")->references("id");
        });
    }
```
3. Jalankan migrasi
4. Edit model

Customer
```php
/** @mixin App
 * @property string id
 * @property string name
 * @property string email
 */
class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = "id";
    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    // Add relationship
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, "customer_id", "id");
    }
}
```

Wallet
```php
/**
 * @mixin App
 * @property integer id
 * @property string customer_id
 * @property integer amount
 */
class Wallet extends Model
{
    protected $table = 'wallets';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;

    public $timestamps = false;

    // add foreign
    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }
}
```
5. Buat seeder untuk wallet dan customer

customer
```php
public function run(): void
    {
        $customer = new Customer();
        $customer->id = "ALDO";
        $customer->name = "Aldo";
        $customer->email = "aldo@gmail.com";
        $customer->save();
    }
```

Wallet
```php
public function run(): void
    {
        $wallet = new Wallet();
        $wallet->amount = 1000000;
        $wallet->id = "ALDO";
        $wallet->save();
    }
```

6. Kemudian untuk testnya:
```php
public function testOneToOne()
    {
        // seed data
        $this->seed([
            CustomerSeeder::class,
            WalletSeeder::class
        ]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);

        // get wallet
        $wallet = $customer->wallet;
        self::assertNotNull($wallet);

        var_dump($wallet->amount);

    }
```
Jadi dengan demikian kita bisa memanggil wallet dari customer

## One To Many
Untuk One To Many mirip dengan One To One, bedanya method nya menggunakan hasMany

Untuk contohnya nanti adalah relasi product ke category.
1. Buat model Product
```shell
php artisan make:model Product --migration --seed
```
2. Define schela product
```php
public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('id', 100)->nullable(false)->primary();
            $table->string('name', 100)->nullable(false);
            $table->text('description')->nullable();
            $table->integer('price')->nullable(false)->default(0);
            $table->integer('stock')->nullable(false)->default(0);
            $table->string('category_id', 100)->nullable(false);
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }
```
3. Jalankan migrasi
4. Edit model

Category
```php
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

    // Add relation to products
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, "category_id", "id");
    }

    protected static function boot():void
    {
        parent::boot();
        self::addGlobalScope(new IsActiveScope());
    }
}
```

products
```php
class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = "id";
    protected $keyType = "string";

    public $incrementing = false;

    public $timestamps = false;

    // add relation to category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
```
5. Buat seeder

Category
```php
public function run(): void
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = true;
        $category->save();
    }
```

Product
```php
public function run(): void
    {
        $product = new Product();
        $product->id = "1";
        $product->name = "Product 1";
        $product->description = "Product 1 description";
        $product->category_id = "FOOD";
        $product->save();
    }
```

6. Penggunaan
```php
public function testOneToMany()
    {
        // seed data
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class
        ]);

        $category = Category::query()->find("FOOD");
        self::assertNotNull($category);

        // find product
        $products = $category->products;
        self::assertNotNull($products);

        self::assertCount(1, $products);
        Log::info($products);
    }
```

```php
public function testOneToMany()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        // relation to category
        $category = $product->category;
        self::assertNotNull($category);

        Log::info($category);
    }
```
