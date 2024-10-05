<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property int $stock
 * @property string $category_id
 * @property-read \App\Models\Category $category
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStock($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = "id";
    protected $keyType = "string";

    protected $fillable = [
        "id", "name", "description"
    ];

    public $incrementing = false;

    public $timestamps = false;

    // add relation to category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
