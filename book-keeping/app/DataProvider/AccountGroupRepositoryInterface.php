<?php

namespace App\DataProvider;

interface AccountGroupRepositoryInterface
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
    public function create(string $bookId, string $accountType, string $title, int $bk_uid, int $bk_code) : string;
}
