<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Customer> $likedByCustomer
 * @property-read int|null $liked_by_customer_count
 * @property-read \App\Models\Like $pivot
 * @property-read \App\Models\Image|null $image
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
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

    protected $hidden = [
      'category_id'
    ];

    public $incrementing = false;

    public $timestamps = false;

    // add relation to category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    // add relation HasMany to review
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    // Add BelongsToMany
    public function likedByCustomer(): BelongsToMany
    {
        return $this->belongsToMany(
            Customer::class,
            'customers_likes_products',
            'product_id',
            'customer_id')->withPivot("created_at")
            ->using(Like::class);
    }

    // Add Morphone
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Add One of Many Polymorphic
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

    // Add many to many polymorphic
    public function tags(): BelongsToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
