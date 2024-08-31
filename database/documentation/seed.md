# Seed

Pada laravel dapat melakukan seeder. Hal ini bisa dengan cara membuat file seeder
```
php artisan make:seeder CategorySeeder
```

CategorySeeder.php
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            "id" => "SMARTPHONE",
            "name" => "Smartphone",
            "created_at" => "2024-08-18 10:10:10"
        ]);

        DB::table('categories')->insert([
            "id" => "FOOD",
            "name" => "Food",
            "created_at" => "2022-08-18 10:10:10"
        ]);

        DB::table('categories')->insert([
            "id" => "LAPTOP",
            "name" => "Laptop",
            "created_at" => "2024-08-18 10:10:10"
        ]);

        DB::table('categories')->insert([
            "id" => "FASHION",
            "name" => "Fashion",
            "created_at" => "2024-08-18 10:10:10"
        ]);
    }
}

```

Kemudian untuk menjalankan seeder:
```
php artisan db:seed --class=CategorySeeder
```
