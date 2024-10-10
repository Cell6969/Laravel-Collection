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
## Query Builder Relationship
Ketika melakukan relations antar model menggunakan eloquent, bisa juga dilakukan proses CRUD dalam relasi tersebut.

Contoh insert:
```php
public function testOneToOneQuery()
    {
        $customer = new Customer();
        $customer->id = "ALDO";
        $customer->name = "Aldo";
        $customer->email = "aldo@gmail.com";
        $customer->save();

        // insert data to wallet of customer
        $wallet = new Wallet();
        $wallet->amount = 1_000_000;
        $customer->wallet()->save($wallet);

        self::assertNotNull($wallet->customer_id);
    }
```

Untuk case one to many
```php
public function testOneToManyQuery()
    {
        $category = new Category();
        $category->id = "FOOD";
        $category->name = "Food";
        $category->description = "Food Category";
        $category->is_active = true;
        $category->save();

        $product =[
            [
                "id" => "1",
                "name" => "Product 1",
                "description" => "Product 1 description",
            ],
            [
                "id" => "2",
                "name" => "Product 2",
                "description" => "Product 2 description",
            ],
        ];

        $category->products()->createMany($product);
        self::assertNotNull($category->id);

        Log::info($category);
    }
```

Selain jikalau ingin get query dengan conditional bisa dengan cara berikut:
```php
public function testRelationshipQuery()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::query()->find("FOOD");
        $HasStockproducts = $category->products;
        self::assertCount(1, $HasStockproducts);

        $outOfProducts = $category->products()->where('stock', '<', 0)->get();
        self::assertCount(0, $outOfProducts);
    }
```

## Has One of Many
Ada kondisi dimana kita hanya ingin mengambil salah satu data saja dari relasi One To Many. Hal ini bisa dilakukan oleh query builder atau dari laravel menyediakan Has One of Many
Sebagai contoh implementasi akan menggunakan data products dan category. Dalam hal ini kita ingin mendapatkan product termahal atau termurah dari category.

Edit category model:
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

    protected static function boot(): void
    {
        parent::boot();
        self::addGlobalScope(new IsActiveScope());
    }

    // add has one of many
    public function cheapestProduct(): HasOne
    {
        return $this->hasOne(Product::class, "category_id", "id")->oldest("price"); // oldest = ascending
    }

    public function mostExpensiveProduct(): HasOne
    {
        return $this->hasOne(Product::class, "category_id", "id")->latest("price"); // latest = descending
    }
}
```
Ubah seeder pada product:
```php
public function run(): void
    {
        $product = new Product();
        $product->id = "1";
        $product->name = "Product 1";
        $product->description = "Product 1 description";
        $product->category_id = "FOOD";
        $product->save();

        $product2 = new Product();
        $product2->id = "2";
        $product2->name = "Product 2";
        $product2->description = "Product 2 description";
        $product2->category_id = "FOOD";
        $product2->price = 200;
        $product2->save();
    }
```

Untuk penggunaan :
```php
public function testHasOneOfMany()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $category = Category::query()->find("FOOD");
        self::assertNotNull($category);

        $cheapestProduct = $category->cheapestProduct;
        self::assertNotNull($cheapestProduct);
        self::assertEquals("1", $cheapestProduct->id);
        Log::info($cheapestProduct);

        $mostExpensiveProduct = $category->mostExpensiveProduct;
        self::assertNotNull($mostExpensiveProduct);
        self::assertEquals("2", $mostExpensiveProduct->id);
        Log::info($mostExpensiveProduct);

    }
```

## Has One Through
Ada case dimana ketika kita telah membuat relasi one to one,kita ingin membuat relasi one to one yang melewati dari satu model. Misal customer->wallet dan wallet->virtualAccount. Kita ingin menyambungkan customer ke virtualAccount.

Contoh implementasi:
1. Buat model VirtualAccount
2. Define schema virtualAccount
```php
public function up(): void
    {
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->integerIncrements("id")->nullable(false);
            $table->string("bank", 100)->nullable(false);
            $table->string("va_number", 100)->nullable(false);
            $table->unsignedInteger("wallet_id")->nullable(false);
            $table->foreign("wallet_id")->on("wallets")->references("id");
        });
    }
```
3. Jalankan migrasi
4. Ubah model pada wallet dan virtual_account

wallet
```php
....
 // add relation to virtual account
    public function virtualAccount(): HasOne
    {
        return $this->hasOne(VirtualAccount::class, "wallet_id", "id");
    }
```

virtual_account
```php
class VirtualAccount extends Model
{
    protected $table = 'virtual_accounts';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = false;
    public $timestamps = false;

    // add relation to wallet
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, "wallet_id", "id");
    }
}
```
5. Tambahkan HasOneThrough pada Customer
```php
... 
 // Add HasOneThrough relationship
    public function virtualAccount(): HasOneThrough
    {
        return $this->hasOneThrough(
            VirtualAccount::class, // Virtual Account Model
            Wallet::class, // Wallet Model
            "customer_id", // FK on wallet
            "wallet_id", // FK on Virtual
            "id", // PK on customer table
            "id" // PK on wallet table
        );
    }
```
6. Buat seeder  virtual account
7. Implementasi
```php
public function testHasOneThrough()
    {
        // seed data
        $this->seed([
            CustomerSeeder::class,
            WalletSeeder::class,
            VirtualAccountSeeder::class
        ]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);

        $virtualAccount = $customer->virtualAccount;
        self::assertNotNull($virtualAccount);
        self::assertEquals('BCA', $virtualAccount->bank);
        Log::info($virtualAccount);
    }
```
Jadi dengan demikian dari customer bisa langsung mengakses virtual account melalui wallet

## Has Many Through
Mirip dengan HasOneThrough tetapi kasusnya untuk OneToMany. Sebagai contoh, dari category terdapat product dan dari product terdapat review

Contoh implementasi:
1. Buat model Review
2. Define model review
```php
public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('product_id', 100)->nullable(false);
            $table->unsignedInteger("rating")->nullable(false);
            $table->string("customer_id", 100)->nullable(false);
            $table->text("comment")->nullable();

            $table->foreign("product_id")->on("products")->references("id");
            $table->foreign("customer_id")->on("customers")->references("id");
        });
    }
```
3. Jalankan migrasi. Pada kasus ini product memiliki banyak review dan customer memiliki banyak review
4. Relasikan customer ke review dan product ke review
```php
... 
// Add HasManyThrough
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, "customer_id", "id");
    }
```

```php
... 
// add relation HasMany to review
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }
```
5. Edit table review
```php
class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $keyType = 'bigInt';
    public $incrementing = true;
    public $timestamps = false;

    // add fk relationship
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
```
6. Tambahkan HasManyThrough pada Category
```php
... 
public function reviews():HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            Product::class,
            "category_id",
            "product_id",
            "id",
            "id"
        );
    }
```
dengan demikian dari category bisa mendapatkan banyak review.
7. Buat seeder review
```php
public function run(): void
    {
        $review = new Review();
        $review->product_id = "1";
        $review->customer_id = "ALDO";
        $review->rating = 5;
        $review->comment = "Good";
        $review->save();

        $review2 = new Review();
        $review2->product_id = "2";
        $review2->customer_id = "ALDO";
        $review2->rating = 4;
        $review2->comment = "Not Bad";
        $review2->save();
    }
```
8. Implementasi HasManyThrough
```php
public function testHasManyThrough()
    {
        // seed data
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            ReviewSeeder::class
        ]);

        $category = Category::query()->find("FOOD");
        self::assertNotNull($category);

        $reviews = $category->reviews;
        self::assertNotNull($reviews);
        self::assertCount(2, $reviews);
    }
```

## Many to Many
eloquent juga mendukung relasi many to many pada model. Sebagai contoh adalah fitur likes, dimana Customer bisa likes ke banyak product dan satu product bisa di-likes oleh banyak Customer
Customer->likes->product
1. Buat migrasi untuk table customer yang bisa melakuka likes
```php
public function up(): void
    {
        Schema::create('customers_likes_products', function (Blueprint $table) {
            $table->string('customer_id', 100)->nullable(false);
            $table->string('product_id', 100)->nullable(false);
            $table->primary(['customer_id', 'product_id']);
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }
```
2. Tambahkan BelongsToMany pada customer dan product

Customer
```php
... 
 // Add BelongsToMany
    public function likeProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,  // add relation to product
            'customer_likes_product', // table go through
            'customer_id', // key origin on table go through
            'product_id' // key related on table go through
        );
    }
```
Product
```php
... 
// Add BelongsToMany
    public function likedByCustomer(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customer_likes_products',
            'product_id',
            'customer_id');
    }
```
3. Jalankan migrasi untuk pembuatan table customers_likes_products
4. Ketika melakukan implementasi misal untuk insert data, tidak bisa langsung dikarenakan tidak ada model jembatan. Hal ini bisa dilakukan menggunakan attach
```php
public function testManyToMany()
    {
        $this->seed([CustomerSeeder::class, CategorySeeder::class, ProductSeeder::class]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);


        $customer->likeProducts()->attach("1");
        $products = $customer->likeProducts;
        self::assertCount(1, $products);

        self::assertEquals("1", $products[0]->id);
    }
```
Pada code diatas artinya, kita mencari customer ALDO, kemudian customer aldo tersebut akan melike products dengan id "1". Pada proses attach, dibelakang melakukan insert data ke table customers_likes_products

Untuk case menghapus relasi many to many, karena tidak ada model penghubungnya maka bisa menggunakan detach.
```php
public function testManyToManyDetach()
    {
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        // detach products
        $customer->likeProducts()->detach("1");

        $products = $customer->likeProducts;
        self::assertCount(1, $products);
        Log::info($products);
    }
```

# Pivot
## Intermediate Table
Table yang menghubungkan antara relasi Many to Many disebut Intermediate. Pada pembuatan Many to Many, kadang table Intermediate ini memiliki attribut selain 2 fk.

Contoh ,pada table customers_likes_products tambahkan kolom created_at.

Untuk mengambil data dari intermediate table itu menggunakan pivot attribute. Biasanya pada table intermediate hanya ada 2 attribute yaitu fk 1 dan fk2. Namun jikalau ada tambahan attribute yang ingin diambil bisa menggunakan withPivot.

Customer
```php
... 
public function likeProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,  // add relation to product
            'customers_likes_products', // table go through
            'customer_id', // key origin on table go through
            'product_id' // key related on table go through
        )->withPivot("created_at");
    }
```

kemudian pada implementasi:
```php
public function testPivotAttribute()
    {
        // seed data
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        $products = $customer->likeProducts;

        foreach ($products as $product) {
            $pivot = $product->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);
            Log::info($pivot);
        }
    }
```

Selain itu bisa juga melakukan filter pada pivot, misal hanya ingin mengambil data like seminggu terakhir
```php
... 
public function likeProductsLastWeek(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,  // add relation to product
            'customers_likes_products', // table go through
            'customer_id', // key origin on table go through
            'product_id' // key related on table go through
        )
            ->withPivot("created_at")
            ->wherePivot("created_at", ">=", Date::now()->addDays(-7));
    }
```

unit testnya:
```php
public function testPivotAttributeCondition()
    {
        // seed data
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        $products = $customer->likeProductsLastWeek;
        foreach ($products as $product) {
            $pivot = $product->pivot;
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);
        }
    }
```
## Pivot Model
Jika pada intermediate table memiliki kolom selain fk, baiknya dibuat kan pivot model namun pivot model bukan dari turunan class model melainkan pivot. Hal ini dikarenakan primary key yang dihandle lebih dari 1.

Sebagai Contoh, buat model like.
```php
class Like extends Pivot
{
    protected $table = "customers_likes_products";
    protected $foreignKey = "customer_id";
    protected $relatedKey = "product_id";
    public $timestamps = false;

    public function usesTimestamps(): bool // dikarenakan logic created_at dan updated_at pada pivot
    {
        return false;
    }
    
    // add relation
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
```
Perlu diperhatikan kita harus meng-override method pada usesTimeStamp, dikarenakak by default Pivot Model menggunakan logic updated_at. Kemudian update model Customer dan Product
```php
\App\Models\Customer
... 
public function likeProducts(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,  // add relation to product
            'customers_likes_products', // table go through
            'customer_id', // key origin on table go through
            'product_id' // key related on table go through
        )
            ->withPivot("created_at")
            ->using(Like::class); // add using like class
    }

    public function likeProductsLastWeek(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,  // add relation to product
            'customers_likes_products', // table go through
            'customer_id', // key origin on table go through
            'product_id' // key related on table go through
        )
            ->withPivot("created_at")
            ->wherePivot("created_at", ">=", Date::now()->addDays(-7))
            ->using(Like::class); // add using like class
    }
```

Kemudian pada implementasinya:
```php
public function testPivotModel()
    {
        $this->testManyToMany();

        $customer = Customer::query()->find("ALDO");
        $products = $customer->likeProducts;

        foreach ($products as $product) {
            $pivot = $product->pivot; // => pivot disini sudah berupa object Model Like
            self::assertNotNull($pivot);
            self::assertNotNull($pivot->customer_id);
            self::assertNotNull($pivot->product_id);
            self::assertNotNull($pivot->created_at);

            self::assertNotNull($pivot->customer);
            self::assertNotNull($pivot->product);
            Log::info($pivot);
        }
    }
```
Jika dilihat pada case sebelumnya, kita hanyak bisa mengakses attribut pada pivot namun sekarang karena pivot sudah merupakan object, kita bisa mengakes object customer dan product.

Dengan demikian pivot sudah sama seperti model biasa, dikarenakan sudah menjadi object.

# Polymorphic Relationships
Polymorphic relationships adalah konsep dimana pada 1 table memiliki foreign key ke multiple table tergantung tipe relasinya. Konsep ini sedikit melenceng dari relational database pada umumnya.

tipe relationship:
1. One to One Polymorphic
2. One to Many Polymorphic
3. One of Many Polymorphic
4. Many to Many Polymorphic

untuk mempermudah visualisasi tanda berikut bisa menjadi bantuan:

-> One to One

--> One to Many

---> Many to Many

## One To One Polymorphic
Mirip dengan One to One namun relasinya bisa lebih dari satu model. Misal Customer dan Product punya satu Image. Sehingga model Image akan terpisah untuk Customer dan Product. Namun dengan polymorphic kita bisa membuat 1 model Image namun memiliki 2 FK untuk Customer dan juga Product

Customer <- Image -> Product

1. Buat model Image
2. Define schema Image
```php
public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string("url", 255)->nullable(false);
            $table->string("imageable_id", 100)->nullable(false);
            $table->string("imageable_type", 200)->nullable(false);
            $table->unique(["imageable_id", "imageable_type"]);
        });
    }
```
3. Jalankan migrasi
4. Update model Image
```php
class Image extends Model
{
    protected $table = 'images';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    // add morph
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
```
5. Kemudian update table customer dan product untuk tambahan morphOne
6. Update Image Seeder
7. Penggunaan:
```php
public function testOneToOnePolymorphic()
    {
        $this->seed([CustomerSeeder::class, ImageSeeder::class]);

        $customer = Customer::query()->find("ALDO");
        self::assertNotNull($customer);

        $image = $customer->image;
        self::assertNotNull($image);
        self::assertEquals("https://image.com/1.jpg", $image->url);
    }
```
Untuk bagian product
```php
public function testOneToOnePolymorphic()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class,ImageSeeder::class]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        $image = $product->image;
        self::assertNotNull($image);
        self::assertEquals("https://image.com/2.jpg", $image->url);
    }
```
Dengan demikian dengan 1 model Image, bisa direlasikan dengan Customer maupun Product.
## One To Many Polymorphic
Sebenarnya mirip dengan One To Many namun dia tidak membentuk unique constrain dikarenakan bisa lebih dari satu. Semisal ada data Comment. Pada Product dan Voucher akan memiliki banyak comment.

Product <-- Comment --> Voucher

Artinya pada Product dan Voucher bisa menambahkan comment ke Model Comment yang sama.

Sebelumnya sudah ada table comment, oleh karena itu hanya menambahkan field untuk polymorphic
1. Update schema Comment
```php
 public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('commentable_id', 100)->nullable(false);
            $table->string('commentable_type', 100)->nullable(false);
        });
    }
```
2. Jalankan migrasi
3. Tambahkan MorphTo pada Model Comment
```php
\App\Models\Comment 
... 
// add morph to
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
```
4. Tambahkan attribut MorphMany di Product dan Voucher.
```php
\App\Models\Product 
... 
public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
```
5. Tambahkan  Comment Seeder
```php
private function createCommentsForProducts(): void
    {
        $product = Product::query()->find("1");

        $comment = new Comment();

        $comment->email = "aldo@gmail.com";
        $comment->title = "title";
        $comment->commentable_id = $product->id;
        $comment->commentable_type = Product::class;
        $comment->save();
    }

    private function createCommentsForVoucher()
    {
        $voucher = Voucher::query()->first();

        $comment = new Comment();

        $comment->email = "aldo@gmail.com";
        $comment->title = "title";
        $comment->commentable_id = $voucher->id;
        $comment->commentable_type = Voucher::class;
        $comment->save();
    }
```
6. Impelementasi One To Many Morph
```php
public function testOneToManyPolymorphic()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
            VoucherSeeder::class,
            CommentSeeder::class
        ]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        $comments = $product->comments;
        foreach ($comments as $comment) {
            self::assertEquals(Product::class, $comment->commentable_type);
            self::assertEquals($product->id, $comment->commentable_id);
            Log::info($comment);
        }
    }
```
## One of Many Polymorphic
Seperti Has One of Many, Polymorphic juga mendukung penambahan kondisi jikalau ingin mengambil salah satu data. Semisal dari data Comment, kita ingin mengambil data komen terakhir.

Pada model Product:
```php
\App\Models\Product 
... 
public function latestComment(): MorphOne
    {
        return $this->morphOne(Comment::class, 'commentable')
            ->latest("created_at");
    }

public function oldestComment(): MorphOne
    {
        return $this->morphOne(Comment::class, 'commentable')
            ->oldest("created_at");
    }
```
Kemudian untuk penggunaanya:
```php
 public function testOneOfManyPolymorphic()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
            VoucherSeeder::class,
            CommentSeeder::class
        ]);

        $product = Product::query()->find("1");
        self::assertNotNull($product);

        $comment = $product->latestComment;
        self::assertNotNull($comment);

        $comment = $product->oldestComment;
        self::assertNotNull($comment);
    }
```
## Many to Many Polymorphic
Many to Many juga bisa dilakukan. Semisal ada model Tag, dimana Tag ini bisa digunakan di banyak voucher dan product. Atau sebaliknya satu voucher atau product bisa menggunakan banyak tag.

1. Buat model Tag
2. Define schema Tag
```php
public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->string('id', 100)->nullable(false)->primary();
            $table->string('name', 100)->nullable(false);
        });

        Schema::create('taggables', function (Blueprint $table) {
           $table->string('tag_id', 100)->nullable(false);
           $table->string("taggable_id", 100)->nullable(false);
           $table->string("taggable_type", 100)->nullable(false);
           $table->primary(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }
```

Jadi pertama kita buat table tags kemudian taggables. Taggables disini bertujuan sebagai jembatan dikarenakan many to many.
3. Jalankan migrasi
4. Edit Tag Model
```php
\App\Models\Tag
class Tag extends Model
{
    protected $table = 'tags';
    protected $primaryKey = "id";
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    // many to many polymorph
    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, "taggable");
    }

    public function vouchers(): MorphToMany
    {
        return $this->morphedByMany(Voucher::class, "taggable");
    }
}
```
5. Tambahakn MorphToMany pada product dan voucher
```php
\App\Models\Product
... 
public function tags(): BelongsToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
```
6. Buat tag seeder
```php
\Database\Seeders\TagSeeder
 public function run(): void
    {
        $tag = new Tag();
        $tag->id = "ig";
        $tag->name = "ig";
        $tag->save();

        $product = Product::query()->find("1");
        $product->tags()->save($tag);

        $voucher = Voucher::query()->first();
        $voucher->tags()->save($tag);
    }
```
5. Kemudian untuk penggunaanya
```php
 public function testManyToManyPolymorhpic()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
            VoucherSeeder::class,
            TagSeeder::class
        ]);

        $product = Product::query()->find("1");
        $tags = $product->tags;
        self::assertNotNull($tags);
        self::assertCount(1, $tags);

        foreach ($tags as $tag) {
            self::assertNotNull($tag->id);
            self::assertNotNull($tag->name);

            $vouchers = $tag->vouchers;
            self::assertNotNull($vouchers);
            self::assertCount(1, $vouchers);
        }
    }
```
