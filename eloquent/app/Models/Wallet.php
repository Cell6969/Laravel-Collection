<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;


/**
 * 
 *
 * @property int $id
 * @property string $customer_id
 * @property int $amount
 * @property-read \App\Models\Customer $customer
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Wallet whereId($value)
 * @property-read \App\Models\VirtualAccount|null $virtualAccount
 * @mixin \Eloquent
 */
class Wallet extends Model
{
    protected $table = 'wallets';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = true;

    public $timestamps = false;

    // add foreign
    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class, "customer_id", "id");
    }

    // add relation to virtual account
    public function virtualAccount(): HasOne
    {
        return $this->hasOne(VirtualAccount::class, "wallet_id", "id");
    }
}
