<?php

namespace App\Service;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;

class BookService
{
    /**
     * Book repository instance.
     *
     * @var \App\DataProvider\BookRepositoryInterface
     */
    private $book;

    /**
     * Permission repository instance.
     *
     * @var \App\DataProvider\PermissionRepositoryInterface
     */
    private $permission;

    /**
     * Create a new BookService instance.
     *
     * @param \App\DataProvider\BookRepositoryInterface       $book
     * @param \App\DataProvider\PermissionRepositoryInterface $app
     */
    public function __construct(BookRepositoryInterface $book, PermissionRepositoryInterface $permission)
    {
        $this->book = $book;
        $this->permission = $permission;
    }

    /**
     * Create new Book.
     *
     * @param int    $userId
     * @param string $title
     *
     * @return string $bookId
     */
    public function createBook(int $userId, string $title): string
    {
        $bookId = $this->book->create($title);
        $permissionId = $this->permission->create($userId, $bookId);

        return $bookId;
    }

    /**
     * Retrieve list of accessable Book.
     *
     * @param int $userId
     *
     * @return array
     */
    public function retrieveBookList(int $userId): array
    {
        $booklist = $this->permission->searchBookList($userId);

        return $booklist;
    }

    /**
     * Retrieve default Book.
     *
     * @param int $userId
     *
     * @return string | null
     */
    public function retrieveDefaultBook(int $userId)
    {
        $bookId = $this->permission->findDefaultBook($userId);

        return $bookId;
    }
}
