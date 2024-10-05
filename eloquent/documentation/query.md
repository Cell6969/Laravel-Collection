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
