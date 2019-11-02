<?php

namespace App\Service;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;

class AccountService
{
    /**
     * The key of Asset in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_ASSET = 'asset';

    /**
     * The key of Liability in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_LIABILITY = 'liability';

    /**
     * The key of Expense in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_EXPENSE = 'expense';

    /**
     * The key of Revenue in the "account_type" column.
     *
     * @var string
     */
    const ACCOUNT_TYPE_REVENUE = 'revenue';

    /**
     * Account repository instance.
     *
     * @var \App\DataProvider\AccountRepositoryInterface
     */
    private $account;

    /**
     * Account group repository instance.
     *
     * @var \App\DataProvider\AccountGroupRepositoryInterface
     */
    private $accountGroup;

    /**
     * Create a new AccountService instance.
     *
     * @param \App\DataProvider\AccountRepositoryInterface      $account
     * @param \App\DataProvider\AccountGroupRepositoryInterface $accountGroup
     */
    public function __construct(AccountRepositoryInterface $account, AccountGroupRepositoryInterface $accountGroup)
    {
        $this->account = $account;
        $this->accountGroup = $accountGroup;
    }

    /**
     * Create new Account.
     *
     * @param string $accountGroupId
     * @param string $title
     * @param string $description
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountId
     */
    public function createAccount(string $accountGroupId, string $title, string $description, int $bk_uid = null, int $bk_code = null) : string
    {
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);

        return $accountId;
    }

    /**
     * Create new Account Group.
     *
     * @param string $bookId
     * @param string $accountType
     * @param string $title
     * @param bool   $isCurrent
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountGroupId
     */
    public function createAccountGroup(string $bookId, string $accountType, string $title, bool $isCurrent = false, int $bk_uid = null, int $bk_code = null) : string
    {
        $accountGroupId = $this->accountGroup->create($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);

        return $accountGroupId;
    }

    /**
     * Retrieve list of account.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function retrieveAccounts(string $bookId): array
    {
        $accounts = [];
        $accountList = $this->account->searchAccount($bookId);

        foreach ($accountList as $accountItem) {
            $accounts[$accountItem['account_id']] = $accountItem;
        }

        return $accounts;
    }
}
