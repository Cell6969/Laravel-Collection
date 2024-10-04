<?php

namespace Tests\Feature;

use App\Models\Voucher;
use Database\Seeders\VoucherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

class VoucherTest extends TestCase
{
    public function testCreateVoucher()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->voucher_code = "12345";
        $voucher->save();
        assertNotNull($voucher->id);
    }

    public function testCreateVoucherUUID()
    {
        $voucher = new Voucher();
        $voucher->name = "Sample Voucher";
        $voucher->save();

        self::assertNotNull($voucher->id);
        self::assertNotNull($voucher->voucher_code);
    }

    public function testSoftDelete()
    {
        $this->seed(VoucherSeeder::class);
        $voucher = Voucher::query()->where('name', '=', 'Sample Voucher')->first();
        $voucher->delete();

        $voucher = Voucher::query()->where('name', '=', 'Sample Voucher')->first();
        self::assertNull($voucher);

        // for displaying data affected with soft delete
        $voucher = Voucher::withTrashed()->where('name', '=', 'Sample Voucher')->first();
        self::assertNotNull($voucher);

        // for forcely delete data
        $voucher->forceDelete();
        $voucher = Voucher::query()->where('name', '=', 'Sample Voucher')->first();
        self::assertNull($voucher);
    }
}
