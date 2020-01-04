<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\BudgetRepositoryInterface;

class BudgetRepository implements BudgetRepositoryInterface
{
    /**
     * Create new budget.
     *
     * @param string $bookId
     * @param string $accountId
     * @param string $date
     * @param int    $amount
     *
     * @return string $budgetId
     */
    public function create(string $bookId, string $accountId, string $date, int $amount): string
    {
        $budget = new Budget();
        $budget->book_bound_on = $bookId;
        $budget->account_code = $accountId;
        $budget->date = $date;
        $budget->amount = $amount;
        $budget->save();

        return $budget->budget_id;
    }
}
