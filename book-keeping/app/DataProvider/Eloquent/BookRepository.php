<?php

namespace App\DataProvider\Eloquent;

use App\DataProvider\BookRepositoryInterface;

class BookRepository implements BookRepositoryInterface
{
    /**
     * Create new book and regist the user as its owner.
     *
     * @param string $title
     *
     * @return string $bookId
     */
    public function create(string $title): string
    {
        $book = new Book();
        $book->book_name = $title;
        $book->save();

        return $book->book_id;
    }
}
