<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


/**
 *
 *
 * @property string $id
 * @property string $name
 * @property string $voucher_code
 * @property string $created_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $is_active
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @method static Builder|Voucher active()
 * @method static Builder|Voucher newModelQuery()
 * @method static Builder|Voucher newQuery()
 * @method static Builder|Voucher nonActive()
 * @method static Builder|Voucher onlyTrashed()
 * @method static Builder|Voucher query()
 * @method static Builder|Voucher whereCreatedAt($value)
 * @method static Builder|Voucher whereDeletedAt($value)
 * @method static Builder|Voucher whereId($value)
 * @method static Builder|Voucher whereIsActive($value)
 * @method static Builder|Voucher whereName($value)
 * @method static Builder|Voucher whereVoucherCode($value)
 * @method static Builder|Voucher withTrashed()
 * @method static Builder|Voucher withoutTrashed()
 * @mixin \Eloquent
 */
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

    // Add Morph Attribute
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
