<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\Book;
use App\DataProvider\Eloquent\Permission;
use App\DataProvider\Eloquent\PermissionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_PermissionRepositoryTest extends DataProvider_PermissionRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $permission;

    public function setUp(): void
    {
        parent::setUp();
        $this->permission = new PermissionRepository();
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
        $userId = 11;
        $bookId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $permissionId = $this->permission->create($userId, $bookId);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_permissions', [
            'permission_id'  => $permissionId,
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ]);
    }

    /**
     * @test
     */
    public function findDefaultBook_ReturnBookIdOfDefaultBook()
    {
        $userId = 21;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $bookId_expected = factory(Book::class)->create([
            'book_name' => 'book_name21',
        ])->book_id;
        $permissionId = factory(Permission::class)->create([
            'permitted_user' => $userId,
            'readable_book'  => $bookId_expected,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $bookId_actual = $this->permission->findDefaultBook($userId);

        $this->assertSame($bookId_expected, $bookId_actual);
    }

    /**
     * @test
     */
    public function searchBookList_ReturnedArrayHasKeysAsBookList()
    {
        $userId = 31;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $bookId = factory(Book::class)->create([
            'book_name' => 'book_name31',
        ])->book_id;
        $permissionId = factory(Permission::class)->create([
            'permitted_user' => $userId,
            'readable_book'  => $bookId,
            'modifiable'     => true,
            'is_owner'       => true,
            'is_default'     => true,
        ])->permission_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $bookList = $this->permission->searchBookList($userId);

        $this->assertFalse(count($bookList) === 0);
        if (!(count($bookList) === 0)) {
            $this->assertSame([
                'book_id',
                'book_name',
                'modifiable',
                'is_owner',
                'is_default',
                'created_at',
            ], array_keys($bookList[0]));
        }
    }
}
