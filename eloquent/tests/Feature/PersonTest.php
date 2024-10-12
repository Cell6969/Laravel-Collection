<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use function Laravel\Prompts\select;

class PersonTest extends TestCase
{
    public function testPerson()
    {
        $person = new Person();
        $person->first_name = "aldo";
        $person->last_name = "don";
        $person->save();

        self::assertEquals("aldo don", $person->full_name);

        $person->full_name = "dadang dadang";
        $person->save();

        self::assertEquals("dadang", $person->first_name);
        self::assertEquals("dadang", $person->last_name);

    }

    public function testCastAttribute()
    {
        $person = new Person();
        $person->first_name = "aldo";
        $person->last_name = "don";
        $person->save();

        self::assertNotNull($person->created_at);
        self::assertNotNull($person->updated_at);
        self::assertInstanceOf(Carbon::class, $person->created_at);
        self::assertInstanceOf(Carbon::class, $person->updated_at);
    }

    public function testCustomCasts()
    {
        $person = new Person();
        $person->first_name = "aldo";
        $person->last_name = "don";
        // use custom cast
        $person->address = new Address("jl. kemang", "jakarta", "indonesia", "1111");
        $person->save();

        self::assertNotNull($person->created_at);
        self::assertNotNull($person->updated_at);
        self::assertInstanceOf(Carbon::class, $person->created_at);
        self::assertInstanceOf(Carbon::class, $person->updated_at);

        self::assertEquals("jl. kemang", $person->address->street);
        self::assertEquals("jakarta", $person->address->city);
        self::assertEquals("indonesia", $person->address->country);
        self::assertEquals("1111", $person->address->postal_code);
    }
}
