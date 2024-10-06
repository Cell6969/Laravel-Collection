<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;


/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property-read \App\Models\Wallet|null $wallet
 * @method static Builder|Customer newModelQuery()
 * @method static Builder|Customer newQuery()
 * @method static Builder|Customer query()
 * @method static Builder|Customer whereEmail($value)
 * @method static Builder|Customer whereId($value)
 * @method static Builder|Customer whereName($value)
 * @property-read \App\Models\VirtualAccount|null $virtualAccount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $likeProducts
 * @property-read int|null $like_products_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\Like $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Product> $likeProductsLastWeek
 * @property-read int|null $like_products_last_week_count
 * @property-read \App\Models\Image|null $image
 * @mixin \Eloquent
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

    // Add HasManyThrough
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, "customer_id", "id");
    }

    // Add BelongsToMany
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

    // Add Morphone
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, "imageable");
    }
}
