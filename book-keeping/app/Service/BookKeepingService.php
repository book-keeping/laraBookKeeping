<?php

namespace App\Service;

use Illuminate\Support\Carbon;
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
     * Add a new slip entry as draft.
     *
     * @param string $debit
     * @param string $client
     * @param string $outline
     * @param string $credit
     * @param int    $amount
     * @param string $bookId
     *
     * @return void
     */
    public function createSlipEntryAsDraft(string $debit, string $client, string $outline, string $credit, int $amount, string $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $draftSlips = $this->slip->retrieveDraftSlips($bookId);
        if (empty($draftSlips)) {
            $date = new Carbon();
            $this->slip->createSlipAsDraft($bookId, $outline, $date->format('Y-m-d'), [
                ['debit' => $debit, 'client' => $client, 'outline' => $outline, 'credit' => $credit, 'amount' => $amount],
            ]);
        } else {
            $this->slip->createSlipEntry($draftSlips[0]['slip_id'], $debit, $credit, $amount, $client, $outline);
        }
    }

    /**
     * Delete the specified slip entry.
     *
     * @param string $slipEntryId
     *
     * @return void
     */
    public function deleteSlipEntryAsDraft(string $slipEntryId)
    {
        $slipId = $this->slip->retrieveSlipThatBound($slipEntryId);
        $this->slip->deleteSlipEntry($slipEntryId);
        $slipEntries = $this->slip->retrieveSlipEntriesBoundTo($slipId);
        if (empty($slipEntries)) {
            $this->slip->deleteSlip($slipId);
        }
    }

    /**
     * Retrieve account list.
     *
     * @param bool   $selectableOnly
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAccounts(bool $selectableOnly = false, string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);

        $accounts_menu = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['groups' => []],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['groups' => []],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['groups' => []],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['groups' => []],
        ];

        foreach ($accounts as $accountsKey => $accountsItem) {
            if ((!$selectableOnly) || ($accountsItem['selectable'] == true)) {
                if (!array_key_exists($accountsItem['account_group_id'], $accounts_menu[$accountsItem['account_type']]['groups'])) {
                    $accounts_menu[$accountsItem['account_type']]['groups'][$accountsItem['account_group_id']] = [
                        'title'        => $accountsItem['account_group_title'],
                        'isCurrent'    => $accountsItem['is_current'],
                        'bk_code'      => $accountsItem['account_group_bk_code'],
                        'createdAt'    => $accountsItem['account_group_created_at'],
                        'items'        => [],
                    ];
                }
                $accounts_menu[$accountsItem['account_type']]['groups'][$accountsItem['account_group_id']]['items'][$accountsKey] = [
                    'title'       => $accountsItem['account_title'],
                    'description' => $accountsItem['description'],
                    'bk_code'     => $accountsItem['account_bk_code'],
                    'createdAt'   => $accountsItem['created_at'],
                ];
            }
        }

        return $accounts_menu;
    }

    /**
     * Retrieve draft slips.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveDraftSlips(string $bookId = null): array
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $accounts = $this->account->retrieveAccounts($bookId);
        $slipsList = $this->slip->retrieveDraftSlips($bookId);
        $slips = [];

        foreach ($slipsList as $slipItem) {
            $slips[$slipItem['slip_id']] = [
                'date'         => $slipItem['date'],
                'slip_outline' => $slipItem['slip_outline'],
                'slip_memo'    => $slipItem['slip_memo'],
                'items'        => [],
            ];
            $slipEntriesList = $this->slip->retrieveSlipEntriesBoundTo($slipItem['slip_id']);
            foreach ($slipEntriesList as $slipEntryItem) {
                $slips[$slipItem['slip_id']]['items'][$slipEntryItem['slip_entry_id']] = [
                    'debit'   => ['account_id' => $slipEntryItem['debit'], 'account_title' => $accounts[$slipEntryItem['debit']]['account_title']],
                    'credit'  => ['account_id' => $slipEntryItem['credit'], 'account_title' => $accounts[$slipEntryItem['credit']]['account_title']],
                    'amount'  => $slipEntryItem['amount'],
                    'client'  => $slipEntryItem['client'],
                    'outline' => $slipEntryItem['outline'],
                ];
            }
        }

        return $slips;
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
        $slipEntries = $this->slip->retrieveSlipEntries($fromDate, $toDate, [], $bookId);
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
                        'createdAt' => $accounts[$accountId]['account_group_created_at'],
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
                    'title'     => $accounts[$accountId]['account_title'],
                    'amount'    => $amount,
                    'bk_code'   => $accounts[$accountId]['account_bk_code'],
                    'createdAt' => $accounts[$accountId]['created_at'],
                ];
            }
        }
        $statements['net_income']['amount'] = $statements[AccountService::ACCOUNT_TYPE_REVENUE]['amount'] - $statements[AccountService::ACCOUNT_TYPE_EXPENSE]['amount'];
        $statements['net_asset']['amount'] = $statements[AccountService::ACCOUNT_TYPE_ASSET]['amount'] - $statements[AccountService::ACCOUNT_TYPE_LIABILITY]['amount'];

        return $statements;
    }

    /**
     * Submit Slip with specified date.
     *
     * @param string $date
     * @param string $bookId
     *
     * @return void
     */
    public function submitDraftSlip(string $date, string $bookId = null)
    {
        if (is_null($bookId)) {
            $bookId = $this->book->retrieveDefaultBook(Auth::id());
        }
        $draftSlips = $this->slip->retrieveDraftSlips($bookId);
        if (!empty($draftSlips)) {
            $this->slip->updateDate($draftSlips[0]['slip_id'], $date);
            $this->slip->submitSlip($draftSlips[0]['slip_id']);
        }
    }
}
