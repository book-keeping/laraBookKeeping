<?php

namespace App\DataProvider;

interface PermissionRepositoryInterface
{
    /**
     * Create new permission.
     *
     * @param int    $userId
     * @param string $bookId
     *
     * @return string $permissionId
     */
    public function create(int $userId, string $bookId) : string;

    /**
     * Find default book of the user.
     *
     * @param int $userId
     *
     * @return string | null
     */
    public function findDefaultBook(int $userId);

    /**
     * Search book list that the user can access.
     *
     * @param int $userId
     *
     * @return array
     */
    public function searchBookList(int $userId) : array;
}
