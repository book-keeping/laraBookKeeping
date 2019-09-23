<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\AccountGroupRepositoryInterface;

class AccountGroupRepository implements AccountGroupRepositoryInterface
{
    /**
     * Create new Account Group.
     *
     * @param string $bookId
     * @param string $accountType
     * @param string $title
     * @param int    $bk_uid
     * @param int    $bk_code
     *
     * @return string $accountGroupId
     */
    public function create(string $bookId, string $accountType, string $title, int $bk_uid, int $bk_code) : string
    {
        $accountGroup = new AccountGroup();
        $accountGroup->book_bound_on = $bookId;
        $accountGroup->account_type = $accountType;
        $accountGroup->account_group_title = $title;
        $accountGroup->bk_uid = $bk_uid;
        $accountGroup->bk_code = $bk_code;
        $accountGroup->save();

        return $accountGroup->account_group_id;
    }
}
