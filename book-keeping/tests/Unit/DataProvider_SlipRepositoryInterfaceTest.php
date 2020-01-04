<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_SlipRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline1';
        $date = '2019-07-01';
        $memo = 'memo1';
        $isDraft = true;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, $isDraft);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($slipId));
    }

    /**
     * @test
     */
    public function create_CalledWithNullForNullable()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline2';
        $date = '2019-07-02';
        $isDraft = true;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = $this->slip->create($bookId, $outline, $date, null, $isDraft);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($slipId));
    }
}
