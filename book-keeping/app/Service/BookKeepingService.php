<?php

namespace App\Service;

class BookKeepingService
{
    /**
     * Account service instance.
     *
     * @var \App\Service\AccountService
     */
    public $account;

    /**
     * Book service instance.
     *
     * @var \App\Service\BookService
     */
    public $book;

    /**
     * Budge service instance.
     *
     * @var \App\Service\BudgetService
     */
    public $budget;

    /**
     * Slip service instance.
     *
     * @var \App\Service\SlipService
     */
    public $slip;

    /**
     * Create a new BookKeepingService instance.
     *
     * @param \App\Service\BookService    $book
     * @param \App\Service\AccountService $account
     * @param \App\Service\BudgetService  $budget
     * @param \App\Service\SlipService    $slip
     */
    public function __construct(BookService $book, AccountService $account, BudgetService $budget, SlipService $slip)
    {
        $this->book = $book;
        $this->account = $account;
        $this->budget = $budget;
        $this->slip = $slip;
    }
}
