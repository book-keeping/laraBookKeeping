<?php

namespace App\DataProvider\Eloquent;

class Book extends UuidModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_books';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'book_id';
}
