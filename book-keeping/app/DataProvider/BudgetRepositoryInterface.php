<?php

namespace App\DataProvider;

interface BudgetRepositoryInterface
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
    public function create(string $bookId, string $accountId, string $date, int $amount): string;
}
