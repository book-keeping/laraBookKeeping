<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\Account;
use App\DataProvider\Eloquent\AccountGroup;
use App\DataProvider\Eloquent\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_AccountRepositoryTest extends DataProvider_AccountRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $account;

    public function setUp(): void
    {
        parent::setUp();
        $this->account = new AccountRepository();
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
        $accountGroupId = (string) Str::uuid();
        $title = 'title';
        $description = 'description';
        $bk_uid = 22;
        $bk_code = 1101;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountId = $this->account->create($accountGroupId, $title, $description, $bk_uid, $bk_code);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_accounts', [
            'account_id'                   => $accountId,
            'account_group_bound_on'       => $accountGroupId,
            'account_title'                => $title,
            'description'                  => $description,
            'selectable'                   => true,
            'bk_uid'                       => $bk_uid,
            'bk_code'                      => $bk_code,
        ]);
    }

    /**
     * @test
     */
    public function searchAccount_ReturnedArrayHasKeysAsAccountList()
    {
        $bookId = (string) Str::uuid();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $accountGroupId = factory(AccountGroup::class)->create([
            'book_bound_on'       => $bookId,
            'account_type'        => 'asset',
            'account_group_title' => 'dummy group title',
            'bk_uid'              => 32,
            'bk_code'             => 1200,
            'is_current'          => true,
        ])->account_group_id;
        $accountId = factory(Account::class)->create([
            'account_group_bound_on' => $accountGroupId,
            'account_title'          => 'dummy title',
            'description'            => 'description',
            'selectable'             => true,
            'bk_uid'                 => 32,
            'bk_code'                => 1201,
        ])->account_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $accountList = $this->account->searchAccount($bookId);

        $this->assertFalse(count($accountList) === 0);
        if (!(count($accountList) === 0)) {
            $this->assertSame([
                'account_type',
                'account_group_id',
                'account_group_title',
                'is_current',
                'account_id',
                'account_title',
                'description',
                'selectable',
                'bk_code',
                'created_at',
            ], array_keys($accountList[0]));
        }
    }
}
