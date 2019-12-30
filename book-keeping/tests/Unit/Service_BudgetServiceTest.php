<?php

namespace Tests\Unit;

use App\DataProvider\BudgetRepositoryInterface;
use App\Service\BudgetService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_BudgetServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createBudget_CallRepositoryWithArgumentsAsItIs()
    {
        $bookId = (string) Str::uuid();
        $accountId = (string) Str::uuid();
        $date = '2019-09-01';
        $amount = 10000;
        $budgetId_expected = (string) Str::uuid();
        $budgetMock = Mockery::mock(BudgetRepositoryInterface::class);
        $budgetMock->shouldReceive('create')
            ->once()
            ->with($bookId, $accountId, $date, $amount)
            ->andReturn($budgetId_expected);

        $budget = new BudgetService($budgetMock);
        $budgetId_actual = $budget->createBudget($bookId, $accountId, $date, $amount);

        $this->assertSame($budgetId_expected, $budgetId_actual);
    }
}
