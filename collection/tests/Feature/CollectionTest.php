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

    public function testZip()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->zip($collection2);

        $this->assertEquals([
            collect([1, 4]),
            collect([2, 5]),
            collect([3, 6])
        ], $collection3->all());
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);
        $collection3 = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $collection3->all());
    }

    public function testCombine()
    {
        $collection1 = collect(["name", "country"]);
        $collection2 = collect(["jonathan", "indonesia"]);
        $collection3 = $collection1->combine($collection2);

        $this->assertEqualsCanonicalizing([
            "name" => "jonathan",
            "country" => "indonesia"
        ], $collection3->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $result = $collection->collapse();

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                "name" => "jonathan",
                "hobbies" => ["Coding", "Football"]
            ],
            [
                "name" => "alphonso",
                "hobbies" => ["Reading", "Explore"]
            ]
        ]);

        $result = $collection->flatMap(function($item){
            $hobbies = $item["hobbies"];
            return $hobbies;
        });

        $this->assertEqualsCanonicalizing(["Coding", "Football", "Reading", "Explore"], $result->all());
    }

    public function testStringRepresentation()
    {
        $collection = collect(["jonathan", "dono", "alphonso"]);

        $this->assertEquals("jonathan-dono-alphonso", $collection->join("-"));
        $this->assertEquals("jonathan-dono_alphonso", $collection->join("-", "_"));
        $this->assertEquals("jonathan,dono and alphonso", $collection->join(",", " and "));
    }
}
