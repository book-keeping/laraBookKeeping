<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

abstract class DataProvider_AccountRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $accountGroupId = '0000';
        $title = 'string';
        $description = 'string';
        $bk_uid = 0;
        $bk_code = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountId));
    }

    /**
     * @test
     */
    public function searchAccount_ReturnValueTypeIsArray()
    {
        $bookId = '0000';

        $accountList = $this->account->searchAccount($bookId);

        $this->assertTrue(is_array($accountList));
    }
}
