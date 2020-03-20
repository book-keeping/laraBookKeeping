<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\AccountRepositoryInterface;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * Create new account.
     *
     * @param string $accountGroupId
     * @param string $title
     * @param string $description
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountId
     */
    public function create(string $accountGroupId, string $title, string $description, $bk_uid, $bk_code): string
    {
        $account = new Account();
        $account->account_group_id = $accountGroupId;
        $account->account_title = $title;
        $account->description = $description;
        $account->selectable = true;
        $account->bk_uid = $bk_uid;
        $account->account_bk_code = $bk_code;
        $account->save();

        return $account->account_id;
    }

    /**
     * Search account.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function searchAccount(string $bookId): array
    {
        $list = Account::select(
            'account_type',
            'bk2_0_account_groups.account_group_id',
            'account_group_title',
            'is_current',
            'account_id',
            'account_title',
            'description',
            'selectable',
            'account_bk_code',
            'bk2_0_accounts.created_at',
            'account_group_bk_code',
            'bk2_0_account_groups.created_at as account_group_created_at',
        )
            ->join('bk2_0_account_groups', 'bk2_0_account_groups.account_group_id', '=', 'bk2_0_accounts.account_group_id')
            ->where('book_id', $bookId)
            ->whereNull('bk2_0_account_groups.deleted_at')
            ->whereNull('bk2_0_accounts.deleted_at')
            ->orderBy('account_type')
            ->orderBy('account_group_id')
            ->get()->toArray();

        return $list;
    }
}
