<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property mixed $id
 * @property string $product_id
 * @property int $rating
 * @property string $customer_id
 * @property string|null $comment
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Review whereRating($value)
 * @mixin \Eloquent
 */
class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $keyType = 'integer';
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
