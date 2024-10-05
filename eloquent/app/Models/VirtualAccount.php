<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property int $id
 * @property string $bank
 * @property string $va_number
 * @property int $wallet_id
 * @property-read \App\Models\Wallet $wallet
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount whereVaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VirtualAccount whereWalletId($value)
 * @mixin \Eloquent
 */
class VirtualAccount extends Model
{
    protected $table = 'virtual_accounts';
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $incrementing = false;
    public $timestamps = false;

    // add relation to wallet
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, "wallet_id", "id");
    }
}
