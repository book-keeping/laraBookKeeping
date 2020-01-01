<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_PermissionRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $userId = 10;
        $bookId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $permissionId = $this->permission->create($userId, $bookId);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($permissionId));
    }

    /**
     * @test
     */
    public function findDefaultBook_ReturnValueTypeIsStringOrNull()
    {
        $userId = 20;

        $bookId = $this->permission->findDefaultBook($userId);

        if (is_null($bookId)) {
            $this->assertTrue(is_null($bookId));
        } else {
            $this->assertTrue(is_string($bookId));
        }
    }

    /**
     * @test
     */
    public function searchBookList_ReturnValueTypeIsArray()
    {
        $userId = 30;

        $bookList = $this->permission->searchBookList($userId);

        $this->assertTrue(is_array($bookList));
    }
}
