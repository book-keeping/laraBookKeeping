<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_AccountRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $accountGroupId = (string) Str::uuid();
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
    public function create_CalledWithNullForNullable()
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'string';
        $description = 'string';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, null, null);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($accountId));
    }

    /**
     * @test
     */
    public function searchAccount_ReturnValueTypeIsArray()
    {
        $bookId = (string) Str::uuid();

        $accountList = $this->account->searchAccount($bookId);

        $this->assertTrue(is_array($accountList));
    }
}
