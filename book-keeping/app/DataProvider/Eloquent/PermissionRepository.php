<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    /**
     * Create new permission.
     *
     * @param int    $userId
     * @param string $bookId
     *
     * @return string $permissionId
     */
    public function create($userId, $bookId) : string
    {
        $is_default = $this->isRegisteredUser($userId) ? false : true;
        $is_owner = $this->isRegisteredBook($bookId) ? false : true;
        $permission = new Permission();
        $permission->permitted_user = $userId;
        $permission->readable_book = $bookId;
        $permission->modifiable = true;
        $permission->is_owner = $is_owner;
        $permission->is_default = $is_default;
        $permission->save();

        return $permission->permission_id;
    }

    /**
     * Find default book of the user.
     *
     * @param int $userId
     *
     * @return string | null
     */
    public function findDefaultBook(int $userId)
    {
        $list = Permission::select('book_id')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->whereNull('deleted_at')
            ->where('is_default', true)
            ->first();

        return empty($list) ? null : $list['book_id'];
    }

    /**
     * Search book list that the user can access.
     *
     * @param int $userId
     *
     * @return array
     */
    public function searchBookList(int $userId) : array
    {
        $list = Permission::select('book_id', 'book_name', 'modifiable', 'is_owner', 'is_default', 'bk2_0_books.created_at')
            ->join('bk2_0_books', 'bk2_0_books.book_id', '=', 'bk2_0_permissions.readable_book')
            ->where('permitted_user', $userId)
            ->whereNull('deleted_at')
            ->orderBy('bk2_0_books.created_at')
            ->get()->toArray();

        return $list;
    }

    /**
     * Check if the book is registerd.
     *
     * @param string $bookId
     *
     * @return bool
     */
    private function isRegisteredBook($bookId) : bool
    {
        $count = Permission::where('readable_book', $bookId)->count();

        return $count == 0 ? false : true;
    }

    /**
     * Check if the user is registerd.
     *
     * @param int $userId
     *
     * @return bool
     */
    private function isRegisteredUser($userId) : bool
    {
        $count = Permission::where('permitted_user', $userId)->count();

        return $count == 0 ? false : true;
    }
}
