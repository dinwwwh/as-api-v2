<?php

namespace Tests\Unit\File;

use App\Models\File;
use Storage;
use Tests\TestCase;

class ModelTest extends TestCase
{
    public function test_autoDeleteFile_mechanism()
    {
        Storage::fake('local');
        Storage::put('dinhdjj.txt', 'hello world!!');
        Storage::disk('local')->assertExists('dinhdjj.txt');

        $file =  File::factory()->state([
            'path' => 'dinhdjj.txt',
        ])->create();
        $file->delete();

        Storage::disk('local')->assertMissing('dinhdjj.txt');
    }
}
