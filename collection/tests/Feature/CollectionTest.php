<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = collect([1, 2, 3]);

        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());
    }

    public function testForEach()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        foreach ($collection as $key => $value) {
            $this->assertEquals($key + 1, $value);
        }
    }

    public function testCrud()
    {
        // push 
        $collection = collect([]);
        $collection->push(1, 2, 3);
        $this->assertEqualsCanonicalizing([1, 2, 3], $collection->all());

        // pop
        $result = $collection->pop();

        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collection->all());
    }

    public function testMap()
    {
        $collection = collect([1, 2, 3]);
        $result = $collection->map(function ($item) {
            return $item * 2; // kali 2
        });
        $this->assertEqualsCanonicalizing([2, 4, 6], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(["jonathan"]);
        $result = $collection->mapInto(Person::class);

        $this->assertEquals([new Person("jonathan")], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ["dono", "van"],
            ["jono", "joni"]
        ]);

        $result = $collection->mapSpread(function ($firstname, $lastname) {
            $fullname = $firstname . ' ' . $lastname;
            return new Person($fullname);
        });

        $this->assertEquals([
            new Person("dono van"),
            new Person("jono joni"),
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "name" => "jonathan",
                "department" => "IT"
            ],
            [
                "name" => "dodi",
                "department" => "IT"
            ],
            [
                "name" => "dono",
                "department" => "Marketing"
            ],
        ]);

        $result = $collection->mapToGroups(function ($person) {
            return [
                $person["department"] => $person["name"]
            ];
        });

        $this->assertEquals([
            "IT" => collect(["jonathan", "dodi"]),
            "Marketing" => collect(["dono"])
        ], $result->all());
    }
}
