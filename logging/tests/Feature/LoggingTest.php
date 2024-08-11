<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LoggingTest extends TestCase
{
    public function testLogging()
    {
        Log::info("Ini Info");
        Log::warning("ini warning");
        Log::error("ini error");
        Log::critical("ini critical");

        self::assertTrue(true);
    }

    public function testContext()
    {
        Log::info("Helli info", ["user" => "jonathan"]);

        self::assertTrue(true);
    }

    public function testWithContext()
    {
        Log::withContext(["user" => "jonathan"]);

        Log::info("access");
        Log::info("access");
        Log::info("access");

        self::assertTrue(true);
    }

    public function testChannel()
    {
        $stdlogger = Log::channel('stderr');
        $stdlogger->error("ini error");

        Log::info("ini info");

        self::assertTrue(true);
    }

    public function testFileHandler()
    {
        $filelogger = Log::channel('file');
        $filelogger->info("info");
        $filelogger->warning("warning");
        $filelogger->error("error");
        $filelogger->critical("critical");


        self::assertTrue(true);

    }
}
