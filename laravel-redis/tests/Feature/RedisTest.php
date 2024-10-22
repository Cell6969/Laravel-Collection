<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisTest extends TestCase
{
    public function testPing()
    {
        $response = Redis::command('ping');
        self::assertEquals('PONG', $response);

        // atau langsung commandnya
        $response = Redis::ping();
        self::assertEquals('PONG', $response);
    }

    public function testString()
    {
        Redis::setEx("user", 2, "aldo");
        $response = Redis::get("user");
        self::assertEquals("aldo", $response);

        sleep(2);
        $response = Redis::get("user");
        self::assertNull($response);
    }

    public function testList()
    {
        Redis::del('user');

        // push data ke kanan list user
        Redis::rpush("user", "aldo");
        Redis::rpush("user", "dono");
        Redis::rpush("user", "van");

        // ambil data user dari 0 hingga akhir
        $response = Redis::lrange("user", 0, -1);
        self::assertEquals(["aldo", "dono", "van"], $response);

        // pop up data
        self::assertEquals("aldo", Redis::lpop("user"));
        self::assertEquals("dono", Redis::lpop("user"));
        self::assertEquals("van", Redis::lpop("user"));
    }

    public function testSet()
    {
        Redis::del("user");

        Redis::sadd("user", "aldo");
        Redis::sadd("user", "aldo");
        Redis::sadd("user", "aldo");
        Redis::sadd("user", "vini");
        Redis::sadd("user", "vini");
        Redis::sadd("user", "meca");


        $response = Redis::smembers("user");
        self::assertEquals(["aldo", "vini", "meca"], $response);
    }

    public function testSortedSetTest()
    {
        Redis::del("user");

        Redis::zadd("user", 100, "edgar");
        Redis::zadd("user", 85, "vini");
        Redis::zadd("user", 10, "dolia");

        $response = Redis::zrange("user", 0, -1); // asc
        self::assertEquals(["dolia", "vini", "edgar"], $response);
    }

    public function testHash()
    {
        Redis::del("user");

        Redis::hset("user:1", "name", "aldo");
        Redis::hset("user:1", "email", "aldo@gmail.com");
        Redis::hset("user:1", "age", 30);

        $response = Redis::hgetall("user:1");
        self::assertEquals([
            "name" => "aldo",
            "email" => "aldo@gmail.com",
            "age" => 30,
        ], $response);
    }

    public function testHyperLogLog()
    {
        Redis::pfadd("visitors", "aldo", "dono", "van");
        Redis::pfadd("visitors", "aldo", "ka", "tanya");
        Redis::pfadd("visitors", "bernanr", "ka", "tanya");

        $result = Redis::pfcount("visitors");

        self::assertEquals(6, $result);
    }

    public function testPipeline()
    {
        Redis::pipeline(function ($pipeline) {
            $pipeline->setex("user", 2, "aldo");
            $pipeline->setex("address", 2, "Indonesia");
        });

        $response = Redis::get("user");
        self::assertEquals("aldo", $response);
        $response = Redis::get("address");
        self::assertEquals("Indonesia", $response);
    }

    public function testTransaction()
    {
        Redis::transaction(function ($transaction) {
            $transaction->setex("user", 2, "aldo");
            $transaction->setex("address", 2, "Indonesia");
        });

        $response = Redis::get("user");
        self::assertEquals("aldo", $response);
        $response = Redis::get("address");
        self::assertEquals("Indonesia", $response);
    }

    public function testPublish()
    {
        for ($i = 0; $i < 10; $i++) {
            Redis::publish("channel-1", "Hello Word $i");
            Redis::publish("channel-2", "Good Bye $i");
        }

        self::assertTrue(true);
    }
}
