<?php

namespace App\Service;

use App\DataProvider\BudgetRepositoryInterface;

class BudgetService
{
    /**
     * Budget repository instance.
     *
     * @var \App\DataProvider\BudgetRepositoryInterface
     */
    private $budget;

    /**
     * Create a new BudgetService instance.
     *
     * @param \App\DataProvider\BudgetRepositoryInterface $budget
     */
    public function __construct(BudgetRepositoryInterface $budget)
    {
        $this->budget = $budget;
    }

    /**
     * Create new Budget.
     *
     * @param string $bookId
     * @param string $accountId
     * @param string $date
     * @param int    $amount
     *
     * @return string $budgetId
     */
    public function createBudget(string $bookId, string $accountId, string $date, int $amount) : string
    {
        $budgetId = $this->budget->create($bookId, $accountId, $date, $amount);

        return $budgetId;
    }
}
