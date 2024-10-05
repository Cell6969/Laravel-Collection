<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


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
}
