<?php

namespace Tests\Unit;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_BookKeepingServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function retrieveSlips_RetrieveTheDefaultBookSlipsForThePeriod()
    {
        $fromDate = '2019-09-01';
        $toDate = '2019-09-30';
        $bookId = (string) Str::uuid();
        $userId = 11;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $slipEentryId_1 = (string) Str::uuid();
        $slipEentryId_2 = (string) Str::uuid();
        $slipEentryId_3 = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipId_2 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
            $accountId_3 => ['account_title' => 'accountTitle_3'],
            $accountId_4 => ['account_title' => 'accountTitle_4'],
            $accountId_5 => ['account_title' => 'accountTitle_5'],
            $accountId_6 => ['account_title' => 'accountTitle_6'],
        ];
        $slipEntries = [
            [
                'slip_id'       => $slipId_1,
                'date'          => '2019-09-15',
                'slip_outline'  => 'slipOutline_1',
                'slip_memo'     => 'slipMemo_1',
                'slip_entry_id' => $slipEentryId_1,
                'debit'         => $accountId_1,
                'credit'        => $accountId_2,
                'amount'        => 100,
                'client'        => 'client_1',
                'outline'       => 'outline_1',
            ],
            [
                'slip_id'       => $slipId_2,
                'date'          => '2019-09-25',
                'slip_outline'  => 'slipOutline_2',
                'slip_memo'     => 'slipMemo_2',
                'slip_entry_id' => $slipEentryId_2,
                'debit'         => $accountId_3,
                'credit'        => $accountId_4,
                'amount'        => 2000,
                'client'        => 'client_2',
                'outline'       => 'outline_2',
            ],
            [
                'slip_id'       => $slipId_1,
                'date'          => '2019-09-15',
                'slip_outline'  => 'slipOutline_1',
                'slip_memo'     => 'slipMemo_1',
                'slip_entry_id' => $slipEentryId_3,
                'debit'         => $accountId_5,
                'credit'        => $accountId_6,
                'amount'        => 30000,
                'client'        => 'client_3',
                'outline'       => 'outline_3',
            ],
        ];
        $slips_expected = [
            $slipId_1 => [
                'date'         => '2019-09-15',
                'slip_outline' => 'slipOutline_1',
                'slip_memo'    => 'slipMemo_1',
                'items'        => [
                    $slipEentryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 100,
                        'client'  => 'client_1',
                        'outline' => 'outline_1',
                    ],
                    $slipEentryId_3 => [
                        'debit'   => ['account_id' => $accountId_5, 'account_title' => 'accountTitle_5'],
                        'credit'  => ['account_id' => $accountId_6, 'account_title' => 'accountTitle_6'],
                        'amount'  => 30000,
                        'client'  => 'client_3',
                        'outline' => 'outline_3',
                    ],
                ],
            ],
            $slipId_2 => [
                'date'         => '2019-09-25',
                'slip_outline' => 'slipOutline_2',
                'slip_memo'    => 'slipMemo_2',
                'items'        => [
                    $slipEentryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 2000,
                        'client'  => 'client_2',
                        'outline' => 'outline_2',
                    ],
                ],
            ],
        ];
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        $budgetMock = Mockery::mock(BudgetService::class);
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntries')
            ->once()
            ->with($fromDate, $toDate, $bookId)
            ->andReturn($slipEntries);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveSlips($fromDate, $toDate);

        $this->assertSame($slips_expected, $slips_actual);
    }

    /**
     * @test
     */
    public function retrieveSlips_RetrieveTheSpecifiedBookSlipsForThePeriod()
    {
        $fromDate = '2019-10-01';
        $toDate = '2019-10-31';
        $bookId = (string) Str::uuid();
        $slips_expected = [];
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldNotReceive('retrieveDefaultBook');
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $budgetMock = Mockery::mock(BudgetService::class);
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipEntries')
            ->once()
            ->with($fromDate, $toDate, $bookId)
            ->andReturn([]);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveSlips($fromDate, $toDate, $bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }

    /**
     * @test
     */
    public function retrieveStatements_RetrieveTheDefaultBookStatementsForThePeriod()
    {
        $fromDate = '2019-10-01';
        $toDate = '2019-10-31';
        $bookId = (string) Str::uuid();
        $userId = 11;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $accountId_7 = (string) Str::uuid();
        $accountId_8 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroupId_4 = (string) Str::uuid();
        $accountGroupId_5 = (string) Str::uuid();
        $accountGroupId_6 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'    => $accountGroupId_1,
                'account_group_title' => 'accountGroupTitle_1',
                'is_current'          => 0,
                'account_bk_code'     => 1201,
                'account_title'       => 'accountTitle_1',
            ],
            $accountId_2 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'    => $accountGroupId_1,
                'account_group_title' => 'accountGroupTitle_1',
                'is_current'          => 0,
                'account_bk_code'     => 1202,
                'account_title'       => 'accountTitle_2',
            ],
            $accountId_3 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'    => $accountGroupId_2,
                'account_group_title' => 'accountGroupTitle_2',
                'is_current'          => 0,
                'account_bk_code'     => 2303,
                'account_title'       => 'accountTitle_3',
            ],
            $accountId_4 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id'    => $accountGroupId_3,
                'account_group_title' => 'accountGroupTitle_3',
                'is_current'          => 0,
                'account_bk_code'     => 4104,
                'account_title'       => 'accountTitle_4',
            ],
            $accountId_5 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id'    => $accountGroupId_4,
                'account_group_title' => 'accountGroupTitle_4',
                'is_current'          => 0,
                'account_bk_code'     => 5105,
                'account_title'       => 'accountTitle_5',
            ],
            $accountId_6 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'    => $accountGroupId_5,
                'account_group_title' => 'accountGroupTitle_5',
                'is_current'          => 1,
                'account_bk_code'     => 1106,
                'account_title'       => 'accountTitle_6',
            ],
            $accountId_7 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'    => $accountGroupId_6,
                'account_group_title' => 'accountGroupTitle_6',
                'is_current'          => 1,
                'account_bk_code'     => 2207,
                'account_title'       => 'accountTitle_7',
            ],
            $accountId_8 => [
                'account_type'        => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id'    => $accountGroupId_3,
                'account_group_title' => 'accountGroupTitle_3',
                'is_current'          => 0,
                'account_bk_code'     => 4108,
                'account_title'       => 'accountTitle_8',
            ],
        ];
        $amountFlows = [
            $accountId_1 => ['debit' => 100, 'credit' => 100],
            $accountId_2 => ['debit' => 2100, 'credit' => 2000],
            $accountId_3 => ['debit' => 3000, 'credit' => 3200],
            $accountId_4 => ['debit' => 4000, 'credit' => 4300],
            $accountId_5 => ['debit' => 5400, 'credit' => 5000],
            $accountId_6 => ['debit' => 6500, 'credit' => 0],
            $accountId_7 => ['debit' => 0, 'credit' => 7600],
            $accountId_8 => ['debit' => 8700, 'credit' => 8000],
        ];
        $statements_expected = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['amount' => 6600, 'groups' => [
                $accountGroupId_1 => [
                    'title'     => 'accountGroupTitle_1',
                    'isCurrent' => 0,
                    'amount'    => 100,
                    'bk_code'   => 1200,
                    'items'     => [
                        $accountId_2 => [
                            'title'   => 'accountTitle_2',
                            'amount'  => 100,
                            'bk_code' => 1202,
                        ],
                    ],
                ],
                $accountGroupId_5 => [
                    'title'     => 'accountGroupTitle_5',
                    'isCurrent' => 1,
                    'amount'    => 6500,
                    'bk_code'   => 1100,
                    'items'     => [
                        $accountId_6 => [
                            'title'   => 'accountTitle_6',
                            'amount'  => 6500,
                            'bk_code' => 1106,
                        ],
                    ],
                ],
            ]],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['amount' => 7800, 'groups' => [
                $accountGroupId_2 => [
                    'title'     => 'accountGroupTitle_2',
                    'isCurrent' => 0,
                    'amount'    => 200,
                    'bk_code'   => 2300,
                    'items'     => [
                        $accountId_3 => [
                            'title'   => 'accountTitle_3',
                            'amount'  => 200,
                            'bk_code' => 2303,
                        ],
                    ],
                ],
                $accountGroupId_6 => [
                    'title'     => 'accountGroupTitle_6',
                    'isCurrent' => 1,
                    'amount'    => 7600,
                    'bk_code'   => 2200,
                    'items'     => [
                        $accountId_7 => [
                            'title'   => 'accountTitle_7',
                            'amount'  => 7600,
                            'bk_code' => 2207,
                        ],
                    ],
                ],
            ]],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['amount' => 400, 'groups' => [
                $accountGroupId_3 => [
                    'title'     => 'accountGroupTitle_3',
                    'isCurrent' => 0,
                    'amount'    => 400,
                    'bk_code'   => 4100,
                    'items'     => [
                        $accountId_4 => [
                            'title'   => 'accountTitle_4',
                            'amount'  => -300,
                            'bk_code' => 4104,
                        ],
                        $accountId_8 => [
                            'title'   => 'accountTitle_8',
                            'amount'  => 700,
                            'bk_code' => 4108,
                        ],
                    ],
                ],
            ]],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['amount' => -400, 'groups' => [
                $accountGroupId_4 => [
                    'title'     => 'accountGroupTitle_4',
                    'isCurrent' => 0,
                    'amount'    => -400,
                    'bk_code'   => 5100,
                    'items'     => [
                        $accountId_5 => [
                            'title'   => 'accountTitle_5',
                            'amount'  => -400,
                            'bk_code' => 5105,
                        ],
                    ],
                ],
            ]],
            'current_net_asset'                    => ['amount' => -1100],
            'net_income'                           => ['amount' => -800],
            'net_asset'                            => ['amount' => -1200],
        ];
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        $budgetMock = Mockery::mock(BudgetService::class);
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveAmountFlows')
            ->once()
            ->with($fromDate, $toDate, $bookId)
            ->andReturn($amountFlows);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $statements_actual = $BookKeeping->retrieveStatements($fromDate, $toDate);

        $this->assertSame($statements_expected, $statements_actual);
    }

    /**
     * @test
     */
    public function retrieveStatements_RetrieveTheSpecifiedBookStatementsForThePeriod()
    {
        $fromDate = '2019-10-01';
        $toDate = '2019-10-31';
        $bookId = (string) Str::uuid();
        $statements_expected = [
            AccountService::ACCOUNT_TYPE_ASSET     => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_LIABILITY => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_EXPENSE   => ['amount' => 0, 'groups' => []],
            AccountService::ACCOUNT_TYPE_REVENUE   => ['amount' => 0, 'groups' => []],
            'current_net_asset'                    => ['amount' => 0],
            'net_income'                           => ['amount' => 0],
            'net_asset'                            => ['amount' => 0],
        ];
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldNotReceive('retrieveDefaultBook');
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $budgetMock = Mockery::mock(BudgetService::class);
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveAmountFlows')
            ->once()
            ->with($fromDate, $toDate, $bookId)
            ->andReturn([]);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $statements_actual = $BookKeeping->retrieveStatements($fromDate, $toDate, $bookId);

        $this->assertSame($statements_expected, $statements_actual);
    }
}
