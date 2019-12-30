<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_BudgetRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $bookId = (string) Str::uuid();
        $accountId = (string) Str::uuid();
        $date = '2019-09-01';
        $amount = 10000;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $budgetId = $this->budget->create($bookId, $accountId, $date, $amount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($budgetId));
    }
}
