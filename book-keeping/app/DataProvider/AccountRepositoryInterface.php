<?php

namespace App\DataProvider;

interface AccountRepositoryInterface
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
    public function create(string $accountGroupId, string $title, string $description, int $bk_uid, int $bk_code): string;

    /**
     * Search account.
     *
     * @param string $bookId
     *
     * @return array
     */
    public function searchAccount(string $bookId): array;
}
