<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Scopes\IsActiveScope;
use App\Models\Wallet;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ReviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class CategoryTest extends TestCase
{
    public function testInsert()
    {
        $category = new Category();
        $category->id = "GADGET";
        $category->name = "Gadget";
        $result = $category->save();

        self::assertTrue($result);
    }

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

    public function testFind()
    {
        $this->seed(CategorySeeder::class);
        $category = Category::query()->find("FOOD");
        self::assertNotNull($category);
        self::assertEquals("FOOD", $category->id);
        self::assertEquals("Food", $category->name);
        self::assertEquals("Food Category", $category->description);
    }

    public function testUpdate()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->find("FOOD");
        $category->name = "Food Updated";

        $result = $category->update();
        self::assertTrue($result);
    }

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
        $categories->each(function ($category) {
            self::assertNull($category->description);

            $category->description = "Updated";
            $category->update();
        });
    }

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

    public function testDelete()
    {
        $this->seed(CategorySeeder::class);

        $category = Category::query()->find("FOOD");
        $result = $category->delete();
        assertTrue($result);

        $total = Category::query()->count();
        self::assertEquals(0, $total);
    }

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

    public function testRelationshipQuery()
    {
        $this->seed([CategorySeeder::class, ProductSeeder::class]);

        $category = Category::query()->find("FOOD");
        $HasStockproducts = $category->products;
        self::assertCount(1, $HasStockproducts);

        $outOfProducts = $category->products()->where('stock', '<', 0)->get();
        self::assertCount(0, $outOfProducts);
    }

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
        Log::info($reviews);
    }

    public function testQueryRelations()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $category = Category::query()->find("FOOD");
        $products = $category->products()->where("price", ">=", 100)->get();
        self::assertCount(1, $products);
        self::assertEquals("2", $products[0]->id);
    }

    public function testQueryAggregateRelationships()
    {
        $this->seed([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        $category = Category::query()->find("FOOD");
        $products = $category->products()->count();

        self::assertEquals(2, $products);

        $total = $category->products()->where("price", ">=", 100)->count();
        self::assertEquals(1, $total);
    }
}
