<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class DataProvider_Eloquent_BookRepositoryTest extends DataProvider_BookRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = new BookRepository();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
    {
        $title = 'string';

        $bookId = $this->book->create($title);

        $this->assertDatabaseHas('bk2_0_books', [
            'book_id'   => $bookId,
            'book_name' => $title,
        ]);
    }
}
