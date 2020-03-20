<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;

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

    /**
     * Retrieve slips between the specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveSlips(string $fromDate, string $toDate, string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);
        $slipEntries = $this->slip->retrieveSlipEntries($fromDate, $toDate, $bookId);
        $slips = [];

        foreach ($slipEntries as $entry) {
            if (!array_key_exists($entry['slip_id'], $slips)) {  /* This is the first time that the entry which belongs to the slip appears. */
                $slips[$entry['slip_id']] = [
                    'date'         => $entry['date'],
                    'slip_outline' => $entry['slip_outline'],
                    'slip_memo'    => $entry['slip_memo'],
                    'items'        => [],
                ];
            }
            $slips[$entry['slip_id']]['items'][$entry['slip_entry_id']] = [
                'debit'   => ['account_id' => $entry['debit'], 'account_title' => $accounts[$entry['debit']]['account_title']],
                'credit'  => ['account_id' => $entry['credit'], 'account_title' => $accounts[$entry['credit']]['account_title']],
                'amount'  => $entry['amount'],
                'client'  => $entry['client'],
                'outline' => $entry['outline'],
            ];
        }

        return $slips;
    }

    /**
     * Retrieve amount changes between the specified date.
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveStatements(string $fromDate, string $toDate, string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);
        $amountFlows = $this->slip->retrieveAmountFlows($fromDate, $toDate, $bookId);
        $statements = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['amount' => 0, 'groups' => []],
            'current_net_asset'                    => ['amount' => 0],
            'net_income'                           => ['amount' => 0],
            'net_asset'                            => ['amount' => 0],
        ];

        foreach ($amountFlows as $accountId => $sumVaule) {
            if (($sumVaule['debit'] - $sumVaule['credit']) != 0) {
                $accountType = $accounts[$accountId]['account_type'];
                if (($accountType == AccountService::ACCOUNT_TYPE_ASSET) || ($accountType == AccountService::ACCOUNT_TYPE_EXPENSE)) {
                    $amount = $sumVaule['debit'] - $sumVaule['credit'];
                } else {
                    $amount = $sumVaule['credit'] - $sumVaule['debit'];
                }
                $accountGroupId = $accounts[$accountId]['account_group_id'];
                $statements[$accountType]['amount'] += $amount;
                if (!array_key_exists($accountGroupId, $statements[$accountType]['groups'])) {  /* This is the first time that the account which belongs to the group appears. */
                    $statements[$accountType]['groups'][$accountGroupId] = [
                        'title'     => $accounts[$accountId]['account_group_title'],
                        'isCurrent' => $accounts[$accountId]['is_current'],
                        'amount'    => 0,
                        'bk_code'   => $accounts[$accountId]['account_group_bk_code'],
                        'items'     => [],
                    ];
                }
                $statements[$accountType]['groups'][$accountGroupId]['amount'] += $amount;
                if ($accounts[$accountId]['is_current']) {
                    if ($accountType == AccountService::ACCOUNT_TYPE_ASSET) {
                        $statements['current_net_asset']['amount'] += $amount;
                    } elseif ($accountType == AccountService::ACCOUNT_TYPE_LIABILITY) {
                        $statements['current_net_asset']['amount'] -= $amount;
                    } else {
                    }
                }
                $statements[$accountType]['groups'][$accountGroupId]['items'][$accountId] = [
                    'title'   => $accounts[$accountId]['account_title'],
                    'amount'  => $amount,
                    'bk_code' => $accounts[$accountId]['account_bk_code'],
                ];
            }
        }
        $statements['net_income']['amount'] = $statements[AccountService::ACCOUNT_TYPE_REVENUE]['amount'] - $statements[AccountService::ACCOUNT_TYPE_EXPENSE]['amount'];
        $statements['net_asset']['amount'] = $statements[AccountService::ACCOUNT_TYPE_ASSET]['amount'] - $statements[AccountService::ACCOUNT_TYPE_LIABILITY]['amount'];

        return $statements;
    }
}
