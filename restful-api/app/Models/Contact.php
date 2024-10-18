<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUserId($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
      "first_name",
      "last_name",
      "email",
      "phone",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'contact_id','id');
    }
}
