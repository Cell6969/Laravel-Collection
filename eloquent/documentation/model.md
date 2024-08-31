# Model

Model adalah representasi tabel di database. Model biasanya dibuat di folder *app\Models*. Untuk membuat model bisa menggunakan periintah berikut:
```
php artisan make:model Category --migration --seed
```

Dengan demikian secara otomatis akan membuat table category beserta file migration dan file seed nya. Perlu diperhatikan untuk mengatur model pada laravel. Beberapa attribut harus disesuaikan sesuai kebutuhan, sebagai contoh:
```php
class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = "id";
    protected $keyType = "string";
    public $incrementing = false;
    public $timestamp = false;
}
```
Penjelaasan pada attribut diatas:
1. Pada $table berarti merujuk pada table model.
2. Primary key berarti menentukan kolom yang menjadi primary key pada table
3. keyType bertujuan untuk menentukan tipe data pada primary key
4. incrementing untuk membuat primaryKey automatis increment (true/false)
5. timestamp untuk membuat created_at dan updated_at otomatis (true/false)

Kemudian edit migration file menjadi:
```php
 public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->string("id", 100)->nullable(false)->primary();
            $table->string("name", 100)->nullable(false);
            $table->text("description")->nullable();
            $table->timestamp("created_at")->nullable(false)->useCurrent();
        });
    }
```

Kemudian jalankan migration.