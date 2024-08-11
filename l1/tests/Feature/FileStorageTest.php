<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class FileStorageTest extends TestCase
{
    public function testStorage()
    {
        $filesystem = Storage::disk("local");

        $filesystem->put("file.txt", "jonathan joe"); // untuk membuat file dan menaruhnya 

        $content = $filesystem->get("file.txt"); // untuk mengambil isi konten pada file

        assertEquals("jonathan joe", $content);
    }

    public function testPublic()
    {
        $filesystem = Storage::disk("public");

        $filesystem->put("file.txt", "jonathan");

        $content = $filesystem->get("file.txt");

        self::assertEquals("jonathan", $content);
    }
}
