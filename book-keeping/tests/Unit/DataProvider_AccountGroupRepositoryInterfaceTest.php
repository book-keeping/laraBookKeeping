<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

abstract class DataProvider_AccountGroupRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $bookId = '0000';
        $accountType = 'asset';
        $title = 'string';
        $isCurrent = true;
        $bk_uid = 0;
        $bk_code = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountGroupId));
    }
}
