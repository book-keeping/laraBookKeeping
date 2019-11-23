<?php

namespace Tests\Unit;

use App\DataProvider\BookRepositoryInterface;
use App\DataProvider\PermissionRepositoryInterface;
use App\Service\BookService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_BookServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createBook_CreateBookAndRegistPermissionByCallRepository()
    {
        $userId = 1;
        $title = 'title';
        $bookId_expected = (string) Str::uuid();
        $permissionId = (string) Str::uuid();
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $bookMock->shouldReceive('create')
            ->once()
            ->with($title)
            ->andReturn($bookId_expected);
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('create')
            ->once()
            ->with($userId, $bookId_expected)
            ->andReturn($permissionId);
        $book = new BookService($bookMock, $permissionMock);
        $bookId_actual = $book->createBook($userId, $title);

        $this->assertSame($bookId_expected, $bookId_actual);
    }

    /**
     * @test
     */
    public function retrieveBookList_CallRepositoryWithArgumentsAsItIs()
    {
        $userId = 1;
        $booklist_expected = [
            ['book_id' => (string) Str::uuid(), 'book_name' => 'book1', 'modifiable' => true, 'is_owner' => true, 'is_default' => true, 'created_at' => new Carbon()],
            ['book_id' => (string) Str::uuid(), 'book_name' => 'book2', 'modifiable' => true, 'is_owner' => false, 'is_default' => false, 'created_at' => new Carbon()],
        ];
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('searchBookList')
            ->once()
            ->with($userId)
            ->andReturn($booklist_expected);

        $book = new BookService($bookMock, $permissionMock);
        $booklist_actual = $book->retrieveBookList($userId);

        $this->assertSame($booklist_expected, $booklist_actual);
    }

    /**
     * @test
     */
    public function retrieveDefaultBook_CallRepositoryWithArgumentsAsItIs()
    {
        $userId = 1;
        $bookId_expected = (string) Str::uuid();
        $bookMock = Mockery::mock(BookRepositoryInterface::class);
        $permissionMock = Mockery::mock(PermissionRepositoryInterface::class);
        $permissionMock->shouldReceive('findDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId_expected);

        $book = new BookService($bookMock, $permissionMock);
        $bookId_actual = $book->retrieveDefaultBook($userId);

        $this->assertSame($bookId_expected, $bookId_actual);
    }
}
