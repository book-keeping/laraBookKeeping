<?php

namespace Tests\Unit;

use App\DataProvider\AccountGroupRepositoryInterface;
use App\DataProvider\AccountRepositoryInterface;
use App\Service\AccountService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_AccountServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createAccount_CallRepositoryWithArgumentsAsItIs()
    {
        $accountGroupId = (string) Str::uuid();
        $title = 'title';
        $description = 'dscription';
        $bk_uid = 22;
        $bk_code = 1101;
        $accountId_expected = (string) Str::uuid();
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('create')
            ->once()
            ->with($accountGroupId, $title, $description, $bk_uid, $bk_code)
            ->andReturn($accountId_expected);

        $account = new AccountService($accountMock, $accountGroupMock);
        $accountId_actual = $account->createAccount($accountGroupId, $title, $description, $bk_uid, $bk_code);

        $this->assertSame($accountId_expected, $accountId_actual);
    }

    /**
     * @test
     */
    public function createAccountGroup_CallRepositoryWithArgumentsAsItIs()
    {
        $bookId = (string) Str::uuid();
        $accountType = 'asset';
        $title = 'title';
        $isCurrent = true;
        $bk_uid = 22;
        $bk_code = 1101;
        $accountGroupId_expected = (string) Str::uuid();
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountGroupMock->shouldReceive('create')
            ->once()
            ->with($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code)
            ->andReturn($accountGroupId_expected);

        $account = new AccountService($accountMock, $accountGroupMock);
        $accountGroupId_actual = $account->createAccountGroup($bookId, $accountType, $title, $isCurrent, $bk_uid, $bk_code);

        $this->assertSame($accountGroupId_expected, $accountGroupId_actual);
    }

    /**
     * @test
     */
    public function retrieveAccounts_CallRepositoryAndChangeArrayFormat()
    {
        $bookId = (string) Str::uuid();
        $account_id_1 = (string) Str::uuid();
        $account_id_2 = (string) Str::uuid();
        $account_id_3 = (string) Str::uuid();
        $accountItem_1 = ['account_id' => $account_id_1, 'account_title' => 'title1', 'selectable' => true, 'account_bk_code' => 1101];
        $accountItem_2 = ['account_id' => $account_id_2, 'account_title' => 'title2', 'selectable' => true, 'account_bk_code' => 1201];
        $accountItem_3 = ['account_id' => $account_id_3, 'account_title' => 'title3', 'selectable' => false, 'account_bk_code' => 1102];
        $accountGroupMock = Mockery::mock(AccountGroupRepositoryInterface::class);
        $accountMock = Mockery::mock(AccountRepositoryInterface::class);
        $accountMock->shouldReceive('searchAccount')
            ->once()
            ->with($bookId)
            ->andReturn([$accountItem_1, $accountItem_2, $accountItem_3]);
        $accounts_expected = [$account_id_1 => $accountItem_1, $account_id_2 => $accountItem_2, $account_id_3 => $accountItem_3];

        $account = new AccountService($accountMock, $accountGroupMock);
        $accounts_actual = $account->retrieveAccounts($bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }
}
