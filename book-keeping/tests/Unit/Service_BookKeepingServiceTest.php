<?php

namespace Tests\Unit;

use App\Service\AccountService;
use App\Service\BookKeepingService;
use App\Service\BookService;
use App\Service\BudgetService;
use App\Service\SlipService;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_BookKeepingServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function createSlipEntryAsDraft_CreateNewSlipForTheDefaultBook()
    {
        $bookId = (string) Str::uuid();
        $userId = 29;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 3600;
        $client = 'client37';
        $outline = 'outline38';
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $slipMock->shouldReceive('createSlipAsDraft')
            ->once()
            ->with($bookId, $outline, '2019-12-02', [
                ['debit' => $debit, 'client' => $client, 'outline' => $outline, 'credit' => $credit, 'amount' => $amount],
            ]);
        $slipMock->shouldNotReceive('createSlipEntry');
        Carbon::setTestNow(new Carbon('2019-12-02 09:59:59'));

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->createSlipEntryAsDraft($debit, $client, $outline, $credit, $amount);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function createSlipEntryAsDraft_AddEntryToSlipTheSpecifiedBook()
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 7900;
        $client = 'client80';
        $outline = 'outline81';
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([['slip_id' => $slipId]]);
        $slipMock->shouldNotReceive('createSlipAsDraft');
        $slipMock->shouldReceive('createSlipEntry')
            ->once()
            ->with($slipId, $debit, $credit, $amount, $client, $outline);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->createSlipEntryAsDraft($debit, $client, $outline, $credit, $amount, $bookId);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function deleteSlipEntryAsDraft_DeleteOnlySlipEntryWhenAnotherSlipEntryIsRemaining()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $slipEntryId_r = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipThatBound')
            ->once()
            ->with($slipEntryId)
            ->andReturn($slipId);
        $slipMock->shouldNotReceive('deleteSlipEntry')
            ->once()
            ->with($slipEntryId);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId)
            ->andReturn([
                [
                    'slip_entry_id' => $slipEntryId_r,
                    'slip_id'       => $slipId,
                    'debit'         => $debit,
                    'credit'        => $credit,
                    'amount'        => 1340,
                    'client'        => 'client135',
                    'outline'       => 'outline136',
                ],
            ]);
        $slipMock->shouldNotReceive('deleteSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->deleteSlipEntryAsDraft($slipEntryId);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function deleteSlipEntryAsDraft_DeleteSlipTooWhenTheSlipEntryIsLastOne()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveSlipThatBound')
            ->once()
            ->with($slipEntryId)
            ->andReturn($slipId);
        $slipMock->shouldNotReceive('deleteSlipEntry')
            ->once()
            ->with($slipEntryId);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId)
            ->andReturn([]);
        $slipMock->shouldReceive('deleteSlip')
            ->once()
            ->with($slipId);
        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->deleteSlipEntryAsDraft($slipEntryId);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function retrieveAccounts_RetrieveTheDefaultBookAccountsWhichIsSelectable()
    {
        $bookId = (string) Str::uuid();
        $userId = 184;
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
        $accounts = [
            $accountId_1 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_1,
                'account_group_title'      => 'accountGroupTitle_1',
                'is_current'               => 0,
                'account_id'               => $accountId_1,
                'account_title'            => 'accountTitle_1',
                'description'              => 'description_1',
                'selectable'               => 1,
                'account_bk_code'          => 1201,
                'created_at'               => '2019-12-02 12:00:01',
                'account_group_bk_code'    => 1200,
                'account_group_created_at' => '2019-12-01 12:00:12',
            ],
            $accountId_2 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'         => $accountGroupId_2,
                'account_group_title'      => 'accountGroupTitle_2',
                'is_current'               => 0,
                'account_id'               => $accountId_2,
                'account_title'            => 'accountTitle_2',
                'description'              => 'description_2',
                'selectable'               => 1,
                'account_bk_code'          => 2302,
                'created_at'               => '2019-12-02 12:00:02',
                'account_group_bk_code'    => 2300,
                'account_group_created_at' => '2019-12-01 12:00:23',
            ],
            $accountId_3 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'         => $accountGroupId_2,
                'account_group_title'      => 'accountGroupTitle_2',
                'is_current'               => 0,
                'account_id'               => $accountId_3,
                'account_title'            => 'accountTitle_3',
                'description'              => 'description_3',
                'selectable'               => 1,
                'account_bk_code'          => 2303,
                'created_at'               => '2019-12-02 12:00:03',
                'account_group_bk_code'    => 2300,
                'account_group_created_at' => '2019-12-01 12:00:23',
            ],
            $accountId_4 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'         => $accountGroupId_3,
                'account_group_title'      => 'accountGroupTitle_3',
                'is_current'               => 0,
                'account_id'               => $accountId_4,
                'account_title'            => 'accountTitle_4',
                'description'              => 'description_4',
                'selectable'               => 1,
                'account_bk_code'          => 2404,
                'created_at'               => '2019-12-02 12:00:04',
                'account_group_bk_code'    => 2400,
                'account_group_created_at' => '2019-12-01 12:00:24',
            ],
            $accountId_5 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id'         => $accountGroupId_4,
                'account_group_title'      => 'accountGroupTitle_4',
                'is_current'               => 0,
                'account_id'               => $accountId_5,
                'account_title'            => 'accountTitle_5',
                'description'              => 'description_5',
                'selectable'               => 0,
                'account_bk_code'          => 4105,
                'created_at'               => '2019-12-02 12:00:05',
                'account_group_bk_code'    => 4100,
                'account_group_created_at' => '2019-12-01 12:00:41',
            ],
            $accountId_6 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id'         => $accountGroupId_5,
                'account_group_title'      => 'accountGroupTitle_5',
                'is_current'               => 1,
                'account_id'               => $accountId_6,
                'account_title'            => 'accountTitle_6',
                'description'              => 'description_6',
                'selectable'               => 1,
                'account_bk_code'          => 5106,
                'created_at'               => '2019-12-02 12:00:06',
                'account_group_bk_code'    => 5100,
                'account_group_created_at' => '2019-12-01 12:00:51',
            ],
            $accountId_7 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id'         => $accountGroupId_5,
                'account_group_title'      => 'accountGroupTitle_5',
                'is_current'               => 1,
                'account_id'               => $accountId_7,
                'account_title'            => 'accountTitle_7',
                'description'              => 'description_7',
                'selectable'               => 0,
                'account_bk_code'          => 5107,
                'created_at'               => '2019-12-02 12:00:07',
                'account_group_bk_code'    => 5100,
                'account_group_created_at' => '2019-12-01 12:00:51',
            ],
            $accountId_8 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id'         => $accountGroupId_5,
                'account_group_title'      => 'accountGroupTitle_5',
                'is_current'               => 1,
                'account_id'               => $accountId_8,
                'account_title'            => 'accountTitle_8',
                'description'              => 'description_8',
                'selectable'               => 1,
                'account_bk_code'          => 5108,
                'created_at'               => '2019-12-02 12:00:08',
                'account_group_bk_code'    => 5100,
                'account_group_created_at' => '2019-12-01 12:00:51',
            ],
        ];
        $accounts_expected = [
            'asset' => [
                'groups' => [
                    $accountGroupId_1 => [
                        'title'        => 'accountGroupTitle_1',
                        'isCurrent'    => 0,
                        'bk_code'      => 1200,
                        'createdAt'    => '2019-12-01 12:00:12',
                        'items'        => [
                            $accountId_1 => [
                                'title'       => 'accountTitle_1',
                                'description' => 'description_1',
                                'bk_code'     => 1201,
                                'createdAt'   => '2019-12-02 12:00:01',
                            ],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'groups' => [
                    $accountGroupId_2 => [
                        'title'        => 'accountGroupTitle_2',
                        'isCurrent'    => 0,
                        'bk_code'      => 2300,
                        'createdAt'    => '2019-12-01 12:00:23',
                        'items'        => [
                            $accountId_2 => [
                                'title'       => 'accountTitle_2',
                                'description' => 'description_2',
                                'bk_code'     => 2302,
                                'createdAt'   => '2019-12-02 12:00:02',
                            ],
                            $accountId_3 => [
                                'title'       => 'accountTitle_3',
                                'description' => 'description_3',
                                'bk_code'     => 2303,
                                'createdAt'   => '2019-12-02 12:00:03',
                            ],
                        ],
                    ],
                    $accountGroupId_3 => [
                        'title'        => 'accountGroupTitle_3',
                        'isCurrent'    => 0,
                        'bk_code'      => 2400,
                        'createdAt'    => '2019-12-01 12:00:24',
                        'items'        => [
                            $accountId_4 => [
                                'title'       => 'accountTitle_4',
                                'description' => 'description_4',
                                'bk_code'     => 2404,
                                'createdAt'   => '2019-12-02 12:00:04',
                            ],
                        ],
                    ],
                ],
            ],
            'expense' => [
                'groups' => [],
            ],
            'revenue' => [
                'groups' => [
                    $accountGroupId_5 => [
                        'title'        => 'accountGroupTitle_5',
                        'isCurrent'    => 1,
                        'bk_code'      => 5100,
                        'createdAt'    => '2019-12-01 12:00:51',
                        'items'        => [
                            $accountId_6 => [
                                'title'       => 'accountTitle_6',
                                'description' => 'description_6',
                                'bk_code'     => 5106,
                                'createdAt'   => '2019-12-02 12:00:06',
                            ],
                            $accountId_8 => [
                                'title'       => 'accountTitle_8',
                                'description' => 'description_8',
                                'bk_code'     => 5108,
                                'createdAt'   => '2019-12-02 12:00:08',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $accounts_actual = $BookKeeping->retrieveAccounts(true);

        $this->assertSame($accounts_expected, $accounts_actual);
    }

    /**
     * @test
     */
    public function retrieveAccounts_RetrieveTheSpecifiedBookAccounts()
    {
        $bookId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_1,
                'account_group_title'      => 'accountGroupTitle_1',
                'is_current'               => 0,
                'account_id'               => $accountId_1,
                'account_title'            => 'accountTitle_1',
                'description'              => 'description_1',
                'selectable'               => 1,
                'account_bk_code'          => 1201,
                'created_at'               => '2019-12-02 12:00:01',
                'account_group_bk_code'    => 1200,
                'account_group_created_at' => '2019-12-01 12:00:12',
            ],
            $accountId_2 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'         => $accountGroupId_2,
                'account_group_title'      => 'accountGroupTitle_2',
                'is_current'               => 0,
                'account_id'               => $accountId_2,
                'account_title'            => 'accountTitle_2',
                'description'              => 'description_2',
                'selectable'               => 0,
                'account_bk_code'          => 2302,
                'created_at'               => '2019-12-02 12:00:02',
                'account_group_bk_code'    => 2300,
                'account_group_created_at' => '2019-12-01 12:00:23',
            ],
        ];
        $accounts_expected = [
            'asset' => [
                'groups' => [
                    $accountGroupId_1 => [
                        'title'     => 'accountGroupTitle_1',
                        'isCurrent' => 0,
                        'bk_code'   => 1200,
                        'createdAt' => '2019-12-01 12:00:12',
                        'items'     => [
                            $accountId_1 => [
                                'title'       => 'accountTitle_1',
                                'description' => 'description_1',
                                'bk_code'     => 1201,
                                'createdAt'   => '2019-12-02 12:00:01',
                            ],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'groups' => [
                    $accountGroupId_2 => [
                        'title'     => 'accountGroupTitle_2',
                        'isCurrent' => 0,
                        'bk_code'   => 2300,
                        'createdAt' => '2019-12-01 12:00:23',
                        'items'     => [
                            $accountId_2 => [
                                'title'       => 'accountTitle_2',
                                'description' => 'description_2',
                                'bk_code'     => 2302,
                                'createdAt'   => '2019-12-02 12:00:02',
                            ],
                        ],
                    ],
                ],
            ],
            'expense' => [
                'groups' => [],
            ],
            'revenue' => [
                'groups' => [],
            ],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $accounts_actual = $BookKeeping->retrieveAccounts(false, $bookId);

        $this->assertSame($accounts_expected, $accounts_actual);
    }

    /**
     * @test
     */
    public function retrieveDraftSlips_RetrieveTheDefaultBookDraftSlips()
    {
        $bookId = (string) Str::uuid();
        $userId = 434;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $slipEntryId_3 = (string) Str::uuid();
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
        $slips = [
            ['slip_id' => $slipId_1, 'date' => '2019-10-03', 'slip_outline' => 'slipOutline_3', 'slip_memo' => 'slipMemo_3'],
            ['slip_id' => $slipId_2, 'date' => '2019-10-04', 'slip_outline' => 'slipOutline_4', 'slip_memo' => 'slipMemo_4'],
        ];
        $slipEntries_1 = [
            [
                'slip_entry_id' => $slipEntryId_1,
                'slip_id'       => $slipId_1,
                'debit'         => $accountId_1,
                'credit'        => $accountId_2,
                'amount'        => 4670,
                'client'        => 'client_468',
                'outline'       => 'outline_469',
            ],
        ];
        $slipEntries_2 = [
            [
                'slip_entry_id' => $slipEntryId_2,
                'slip_id'       => $slipId_2,
                'debit'         => $accountId_3,
                'credit'        => $accountId_4,
                'amount'        => 4780,
                'client'        => 'client_479',
                'outline'       => 'outline_480',
            ],
            [
                'slip_entry_id' => $slipEntryId_3,
                'slip_id'       => $slipId_2,
                'debit'         => $accountId_5,
                'credit'        => $accountId_6,
                'amount'        => 4870,
                'client'        => 'client_488',
                'outline'       => 'outline_489',
            ],
        ];
        $slips_expected = [
            $slipId_1 => [
                'date'         => '2019-10-03',
                'slip_outline' => 'slipOutline_3',
                'slip_memo'    => 'slipMemo_3',
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 4670,
                        'client'  => 'client_468',
                        'outline' => 'outline_469',
                    ],
                ],
            ],
            $slipId_2 => [
                'date'         => '2019-10-04',
                'slip_outline' => 'slipOutline_4',
                'slip_memo'    => 'slipMemo_4',
                'items'        => [
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 4780,
                        'client'  => 'client_479',
                        'outline' => 'outline_480',
                    ],
                    $slipEntryId_3 => [
                        'debit'   => ['account_id' => $accountId_5, 'account_title' => 'accountTitle_5'],
                        'credit'  => ['account_id' => $accountId_6, 'account_title' => 'accountTitle_6'],
                        'amount'  => 4870,
                        'client'  => 'client_488',
                        'outline' => 'outline_489',
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn($slips);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId_1)
            ->andReturn($slipEntries_1);
        $slipMock->shouldReceive('retrieveSlipEntriesBoundTo')
            ->once()
            ->with($slipId_2)
            ->andReturn($slipEntries_2);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveDraftSlips();

        $this->assertSame($slips_expected, $slips_actual);
    }

    /**
     * @test
     */
    public function retrieveDraftSlips_RetrieveTheSpecifiedBookDraftSlips()
    {
        $bookId = (string) Str::uuid();
        $slips_expected = [];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([]);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $slips_actual = $BookKeeping->retrieveDraftSlips($bookId);

        $this->assertSame($slips_expected, $slips_actual);
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
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $slipEntryId_3 = (string) Str::uuid();
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
                'slip_entry_id' => $slipEntryId_1,
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
                'slip_entry_id' => $slipEntryId_2,
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
                'slip_entry_id' => $slipEntryId_3,
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
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 100,
                        'client'  => 'client_1',
                        'outline' => 'outline_1',
                    ],
                    $slipEntryId_3 => [
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
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 2000,
                        'client'  => 'client_2',
                        'outline' => 'outline_2',
                    ],
                ],
            ],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
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
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldNotReceive('retrieveDefaultBook');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
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
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_1,
                'account_group_title'      => 'accountGroupTitle_1',
                'is_current'               => 0,
                'account_bk_code'          => 1201,
                'account_title'            => 'accountTitle_1',
                'account_group_bk_code'    => 1200,
                'created_at'               => '2019-12-01 12:00:01',
                'account_group_created_at' => '2019-12-01 12:00:00',
            ],
            $accountId_2 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_1,
                'account_group_title'      => 'accountGroupTitle_1',
                'is_current'               => 0,
                'account_bk_code'          => 1202,
                'account_title'            => 'accountTitle_2',
                'account_group_bk_code'    => 1200,
                'created_at'               => '2019-12-01 12:00:02',
                'account_group_created_at' => '2019-12-01 12:00:00',
            ],
            $accountId_3 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'         => $accountGroupId_2,
                'account_group_title'      => 'accountGroupTitle_2',
                'is_current'               => 0,
                'account_bk_code'          => 2303,
                'account_title'            => 'accountTitle_3',
                'account_group_bk_code'    => 2300,
                'created_at'               => '2019-12-01 12:23:03',
                'account_group_created_at' => '2019-12-01 12:23:00',
            ],
            $accountId_4 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id'         => $accountGroupId_3,
                'account_group_title'      => 'accountGroupTitle_3',
                'is_current'               => 0,
                'account_bk_code'          => 4104,
                'account_title'            => 'accountTitle_4',
                'account_group_bk_code'    => 4100,
                'created_at'               => '2019-12-01 12:41:04',
                'account_group_created_at' => '2019-12-01 12:41:00',
            ],
            $accountId_5 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_REVENUE,
                'account_group_id'         => $accountGroupId_4,
                'account_group_title'      => 'accountGroupTitle_4',
                'is_current'               => 0,
                'account_bk_code'          => 5105,
                'account_title'            => 'accountTitle_5',
                'account_group_bk_code'    => 5100,
                'created_at'               => '2019-12-01 12:51:05',
                'account_group_created_at' => '2019-12-01 12:51:00',
            ],
            $accountId_6 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_ASSET,
                'account_group_id'         => $accountGroupId_5,
                'account_group_title'      => 'accountGroupTitle_5',
                'is_current'               => 1,
                'account_bk_code'          => 1106,
                'account_title'            => 'accountTitle_6',
                'account_group_bk_code'    => 1100,
                'created_at'               => '2019-12-01 12:11:06',
                'account_group_created_at' => '2019-12-01 12:11:00',
            ],
            $accountId_7 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_LIABILITY,
                'account_group_id'         => $accountGroupId_6,
                'account_group_title'      => 'accountGroupTitle_6',
                'is_current'               => 1,
                'account_bk_code'          => 2207,
                'account_title'            => 'accountTitle_7',
                'account_group_bk_code'    => 2200,
                'created_at'               => '2019-12-01 12:22:07',
                'account_group_created_at' => '2019-12-01 12:22:00',
            ],
            $accountId_8 => [
                'account_type'             => AccountService::ACCOUNT_TYPE_EXPENSE,
                'account_group_id'         => $accountGroupId_3,
                'account_group_title'      => 'accountGroupTitle_3',
                'is_current'               => 0,
                'account_bk_code'          => 4108,
                'account_title'            => 'accountTitle_8',
                'account_group_bk_code'    => 4100,
                'created_at'               => '2019-12-01 12:41:08',
                'account_group_created_at' => '2019-12-01 12:41:00',
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
                    'createdAt' => '2019-12-01 12:00:00',
                    'items'     => [
                        $accountId_2 => [
                            'title'     => 'accountTitle_2',
                            'amount'    => 100,
                            'bk_code'   => 1202,
                            'createdAt' => '2019-12-01 12:00:02',
                        ],
                    ],
                ],
                $accountGroupId_5 => [
                    'title'     => 'accountGroupTitle_5',
                    'isCurrent' => 1,
                    'amount'    => 6500,
                    'bk_code'   => 1100,
                    'createdAt' => '2019-12-01 12:11:00',
                    'items'     => [
                        $accountId_6 => [
                            'title'     => 'accountTitle_6',
                            'amount'    => 6500,
                            'bk_code'   => 1106,
                            'createdAt' => '2019-12-01 12:11:06',
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
                    'createdAt' => '2019-12-01 12:23:00',
                    'items'     => [
                        $accountId_3 => [
                            'title'     => 'accountTitle_3',
                            'amount'    => 200,
                            'bk_code'   => 2303,
                            'createdAt' => '2019-12-01 12:23:03',
                        ],
                    ],
                ],
                $accountGroupId_6 => [
                    'title'     => 'accountGroupTitle_6',
                    'isCurrent' => 1,
                    'amount'    => 7600,
                    'bk_code'   => 2200,
                    'createdAt' => '2019-12-01 12:22:00',
                    'items'     => [
                        $accountId_7 => [
                            'title'     => 'accountTitle_7',
                            'amount'    => 7600,
                            'bk_code'   => 2207,
                            'createdAt' => '2019-12-01 12:22:07',
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
                    'createdAt' => '2019-12-01 12:41:00',
                    'items'     => [
                        $accountId_4 => [
                            'title'     => 'accountTitle_4',
                            'amount'    => -300,
                            'bk_code'   => 4104,
                            'createdAt' => '2019-12-01 12:41:04',
                        ],
                        $accountId_8 => [
                            'title'     => 'accountTitle_8',
                            'amount'    => 700,
                            'bk_code'   => 4108,
                            'createdAt' => '2019-12-01 12:41:08',
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
                    'createdAt' => '2019-12-01 12:51:00',
                    'items'     => [
                        $accountId_5 => [
                            'title'     => 'accountTitle_5',
                            'amount'    => -400,
                            'bk_code'   => 5105,
                            'createdAt' => '2019-12-01 12:51:05',
                        ],
                    ],
                ],
            ]],
            'current_net_asset'                    => ['amount' => -1100],
            'net_income'                           => ['amount' => -800],
            'net_asset'                            => ['amount' => -1200],
        ];
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn($accounts);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
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
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldNotReceive('retrieveDefaultBook');
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        $accountMock->shouldReceive('retrieveAccounts')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveAmountFlows')
            ->once()
            ->with($fromDate, $toDate, $bookId)
            ->andReturn([]);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $statements_actual = $BookKeeping->retrieveStatements($fromDate, $toDate, $bookId);

        $this->assertSame($statements_expected, $statements_actual);
    }

    /**
     * @test
     */
    public function submitDraftSlip_SubmitDraftSlipForTheDefaultBook()
    {
        $bookId = (string) Str::uuid();
        $userId = 1045;
        $user = new User();
        $user->id = $userId;
        $this->be($user);
        $slipId = (string) Str::uuid();
        $date = '2020-04-03';
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        $bookMock->shouldReceive('retrieveDefaultBook')
            ->once()
            ->with($userId)
            ->andReturn($bookId);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([['slip_id' => $slipId]]);
        $slipMock->shouldReceive('updateDate')
            ->once()
            ->with($slipId, $date);
        $slipMock->shouldReceive('submitSlip')
            ->once()
            ->with($slipId);

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->submitDraftSlip($date);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function submitDraftSlip_SubmitDraftSlipForTheSpecifiedBookButThereIsNoTarget()
    {
        $bookId = (string) Str::uuid();
        $date = '2020-04-04';
        /** @var \App\Service\BookService|\Mockery\MockInterface $bookMock */
        $bookMock = Mockery::mock(BookService::class);
        /** @var \App\Service\AccountService|\Mockery\MockInterface $accountMock */
        $accountMock = Mockery::mock(AccountService::class);
        /** @var \App\Service\BudgetService|\Mockery\MockInterface $budgetMock */
        $budgetMock = Mockery::mock(BudgetService::class);
        /** @var \App\Service\SlipService|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipService::class);
        $slipMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->with($bookId)
            ->andReturn([]);
        $slipMock->shouldNotReceive('updateDate');
        $slipMock->shouldNotReceive('submitSlip');

        $BookKeeping = new BookKeepingService($bookMock, $accountMock, $budgetMock, $slipMock);
        $BookKeeping->submitDraftSlip($date, $bookId);

        $this->assertTrue(true);
    }
}
