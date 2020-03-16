<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\BudgetRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_BudgetRepositoryTest extends DataProvider_BudgetRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $budget;

    public function setUp(): void
    {
        parent::setUp();
        $this->budget = new BudgetRepository();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
    {
        $bookId = (string) Str::uuid();
        $accountId = (string) Str::uuid();
        $date = '2019-09-01';
        $amount = 10000;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $budgetId = $this->budget->create($bookId, $accountId, $date, $amount);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_budgets', [
            'budget_id'     => $budgetId,
            'book_id'       => $bookId,
            'account_code'  => $accountId,
            'date'          => $date,
            'amount'        => $amount,
        ]);
    }
}
