# Eloquent-Api-Resource

## Database
Buat database baru untuk eloquent, kemudian config pada laravel

## Model
Buat model Category dan Product dimana Category memiliki relasi One To Many ke Product
```shell
php artisan make:model Category --migration --seed
php artisan make:model Product --migration --seed
```
define schema Category:
```php
public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
```

```php
public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable(false);
            $table->bigInteger('price')->nullable(false)->default(0);
            $table->integer('stock')->nullable(false)->default(0);
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->timestamps();
            // fk
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }
```

define model Category:
```php
\App\Models\Category:: 
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    public function product(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }
}
```
```php
\App\Models\Product:: 
class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
```
## Resource
Resource merupakan respresentasi dari cara melakukan transformasi dari Model menjadi array/json. Untuk membuat resource:
```shell
php artisan make:resource <NamaResource>
```
Resource sendiri adalah representasi dari single object data yang ingin dit transform menjadi Array/JSON. Jadi singkatnya klo di controller memanggil model Category maka otomatis Response nya merupakan Resource Model Category
```php
\App\Http\Resources\CategoryResource:: 
/**
 * @mixin Category
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```
Kemudian pada Route:
```php
api.php
Route::get('/categories/{id}', function ($id){
    $category = \App\Models\Category::query()->findOrFail($id);
    return new \App\Http\Resources\CategoryResource($category);
});
```
Jadi kita panggil data menggunakan model, kemudian untuk transformasi nya menggunakan Resource sebagai Output

Kemudian untuk seeder Category:
```php
public function run(): void
    {
        Category::query()->create([
            "name" => "FOOD"
        ]);

        Category::query()->create([
            "name" => "GADGET"
        ]);
    }
```

Kemudian untuk test:
```php
public function testResources()
    {
        $this->seed([CategorySeeder::class]);

        $category = Category::query()->first();

        $this->get("/api/categories/$category->id")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'created_at' => $category->created_at->toJSON(),
                    'updated_at' => $category->updated_at->toJSON(),
                ]
            ]);
    }
```
Jika diperhatikan ada key data. Karena Resource akan me-wrap hasil output dan dimasukkan ke dalam data.

## Resource Collection
By default, Resource yang sudah dibuat bisa kita gunakan untuk menampilkan data multiple object atau JSON array. Hal ini bisa dilakukan menggunakan static method

```php
Route::get('/categories', function () {
    $categories = \App\Models\Category::all();
    return \App\Http\Resources\CategoryResource::collection($categories);
});
```
dengan demikian data collection tersebut akan di wrap kedalam data. Jadi dengan resource bisa outputnya 1 object atau collection.

## Resource Collection Custom
Ada case dimana kita ingin membuat resource collection secara manual tanpa extend class dari ResourceClass. Hal ini bisa dibuat dengan command:
```shell
php artisan make:resource NamaCollection --collection
```

```php
class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "data" => $this->collection,
            "total" => count($this->collection),
        ];
    }
}
```

Kemudian pada route:
```php
Route::get('/categories-custom', function () {
   $categories = \App\Models\Category::all();
   return new \App\Http\Resources\CategoryCollection($categories);
});
```
output yang dihasilkan:
![img.png](img.png)
