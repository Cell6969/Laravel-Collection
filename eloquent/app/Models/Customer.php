<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;


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
}
