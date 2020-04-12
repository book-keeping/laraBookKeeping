<?php

namespace Tests\Unit;

use App\Http\Responder\v1\BaseViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_v1_BaseViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function navilinks_NaviListIsReturned()
    {
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $navilinks = $responder->navilinks();

        $this->assertIsArray($navilinks);
    }

    /**
     * @test
     */
    public function sortAccountInAscendingCodeOrder_ReorderedListIsReturned()
    {
        $groupedList = [
            '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112, 'bk_code' => 4300, 'createdAt' => '2020-03-01 12:43:00',
                'items' => [
                    '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:14'],
                    '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000, 'bk_code' => 4301, 'createdAt' => '2020-03-01 12:43:01'],
                    'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420, 'bk_code' => 4303, 'createdAt' => '2020-03-01 12:43:03'],
                    'fbfa988b-6e74-4a47-b6a0-9068aafcbacb' => ['title' => 'ItemTitle14', 'amount' => 29697, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:11'],
                    'e7971328-7c62-4117-b82f-75b273ec32c1' => ['title' => 'ItemTitle15', 'amount' => 29292, 'bk_code' => null, 'createdAt' => '2020-01-01 12:00:11'],
                ],
            ],
            '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610, 'bk_code' => 4400, 'createdAt' => '2020-01-01 12:44:00',
                'items' => [
                    '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57, 'bk_code' => 4403, 'createdAt' => '2019-12-31 12:44:03'],
                    '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457, 'bk_code' => 4404, 'createdAt' => '2019-12-30 12:44:04'],
                    '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346, 'bk_code' => 4402, 'createdAt' => '2019-12-29 12:44:02'],
                    'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750, 'bk_code' => 4407, 'createdAt' => '2019-12-28 12:44:07'],
                ],
            ],
            '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446, 'bk_code' => 4100, 'createdAt' => '2019-12-27 12:41:00',
                'items' => [
                    '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246, 'bk_code' => 4104, 'createdAt' => '2019-12-26 12:41:04'],
                    '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200, 'bk_code' => 4103, 'createdAt' => '2019-12-25 12:41:03'],
                ],
            ],
            'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                'title' => 'GroupTitle4', 'isCurrent' => 1, 'amount' => 394151, 'bk_code' => 4200, 'createdAt' => '2019-12-24 12:42:00',
                'items' => [
                    '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle33', 'amount' => 394151, 'bk_code' => 4213, 'createdAt' => '2019-12-23 12:42:13'],
                ],
            ],
        ];
        $reordered_expected = [
            'ed88bc2c-8b82-45cf-b276-f2046b7b22e9' => [
                'title' => 'GroupTitle4', 'isCurrent' => 1, 'amount' => 394151, 'bk_code' => 4200, 'createdAt' => '2019-12-24 12:42:00',
                'items' => [
                    '944ddb3d-6cc9-43ad-9ba1-2dffd2ee5b58' => ['title' => 'ItemTitle33', 'amount' => 394151, 'bk_code' => 4213, 'createdAt' => '2019-12-23 12:42:13'],
                ],
            ],
            '9b0c599e-28f8-4974-9ae1-4145f3770f52' => [
                'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 13446, 'bk_code' => 4100, 'createdAt' => '2019-12-27 12:41:00',
                'items' => [
                    '7c707a00-0e95-4ae4-886c-b46678aeb5b5' => ['title' => 'ItemTitle32', 'amount' => 200, 'bk_code' => 4103, 'createdAt' => '2019-12-25 12:41:03'],
                    '515a27c0-ad28-4f08-9ac9-624b5f45c3d6' => ['title' => 'ItemTitle31', 'amount' => 13246, 'bk_code' => 4104, 'createdAt' => '2019-12-26 12:41:04'],
                ],
            ],
            '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112, 'bk_code' => 4300, 'createdAt' => '2020-03-01 12:43:00',
                'items' => [
                    '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000, 'bk_code' => 4301, 'createdAt' => '2020-03-01 12:43:01'],
                    'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420, 'bk_code' => 4303, 'createdAt' => '2020-03-01 12:43:03'],
                    'e7971328-7c62-4117-b82f-75b273ec32c1' => ['title' => 'ItemTitle15', 'amount' => 29292, 'bk_code' => null, 'createdAt' => '2020-01-01 12:00:11'],
                    'fbfa988b-6e74-4a47-b6a0-9068aafcbacb' => ['title' => 'ItemTitle14', 'amount' => 29697, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:11'],
                    '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692, 'bk_code' => null, 'createdAt' => '2020-02-01 12:00:14'],
                ],
            ],
            '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610, 'bk_code' => 4400, 'createdAt' => '2020-01-01 12:44:00',
                'items' => [
                    '6eb9015e-039f-4028-8b2b-3f3279c8849c' => ['title' => 'ItemTitle23', 'amount' => 346, 'bk_code' => 4402, 'createdAt' => '2019-12-29 12:44:02'],
                    '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57, 'bk_code' => 4403, 'createdAt' => '2019-12-31 12:44:03'],
                    '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457, 'bk_code' => 4404, 'createdAt' => '2019-12-30 12:44:04'],
                    'f4c7b564-79e4-4037-9814-ec4bb040c58e' => ['title' => 'ItemTitle24', 'amount' => 750, 'bk_code' => 4407, 'createdAt' => '2019-12-28 12:44:07'],
                ],
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $reordered_actual = $responder->sortAccountInAscendingCodeOrder($groupedList);

        $this->assertSame($reordered_expected, $reordered_actual);
    }

    /**
     * @test
     */
    public function translateAccountListFormat_FormattedAccountListIsReturned()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountId_5 = (string) Str::uuid();
        $accountId_6 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroupId_4 = (string) Str::uuid();
        $accountGroupId_5 = (string) Str::uuid();
        $accounts = [
            'asset' => [
                'groups' => [
                    $accountGroupId_1 => [
                        'isCurrent'    => 0,
                        'bk_code'      => 1200,
                        'createdAt'    => '2019-12-01 12:00:12',
                        'items'        => [
                            $accountId_1 => [
                                'title'    => 'accountTitle_1',
                                'bk_code'  => 1201,
                                'createdAt'=> '2019-12-02 12:00:01',
                            ],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'groups' => [
                    $accountGroupId_2 => [
                        'isCurrent'    => 0,
                        'bk_code'      => 2300,
                        'createdAt'    => '2019-12-01 12:00:23',
                        'items'        => [
                            $accountId_2 => [
                                'title'    => 'accountTitle_2',
                                'bk_code'  => 2302,
                                'createdAt'=> '2019-12-02 12:00:02',
                            ],
                            $accountId_3 => [
                                'title'    => 'accountTitle_3',
                                'bk_code'  => 2303,
                                'createdAt'=> '2019-12-02 12:00:03',
                            ],
                        ],
                    ],
                    $accountGroupId_3 => [
                        'isCurrent'    => 0,
                        'bk_code'      => 2400,
                        'createdAt'    => '2019-12-01 12:00:24',
                        'items'        => [
                            $accountId_4 => [
                                'title'    => 'accountTitle_4',
                                'bk_code'  => 2404,
                                'createdAt'=> '2019-12-02 12:00:04',
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
                    $accountGroupId_4 => [
                        'isCurrent'    => 1,
                        'bk_code'      => 5100,
                        'createdAt'    => '2019-12-01 12:00:51',
                        'items'        => [
                            $accountId_5 => [
                                'title'    => 'accountTitle_5',
                                'bk_code'  => 5105,
                                'createdAt'=> '2019-12-02 12:00:06',
                            ],
                        ],
                    ],
                    $accountGroupId_5 => [
                        'isCurrent'    => 1,
                        'bk_code'      => 5200,
                        'createdAt'    => '2019-12-01 12:00:51',
                        'items'        => [
                            $accountId_6 => [
                                'title'    => 'accountTitle_6',
                                'bk_code'  => 5206,
                                'createdAt'=> '2019-12-02 12:00:06',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $formattedAccountList_expected = [
            $accountId_1 => 'accountTitle_1',
            $accountId_2 => 'accountTitle_2',
            $accountId_3 => 'accountTitle_3',
            $accountId_4 => 'accountTitle_4',
            $accountId_5 => 'accountTitle_5',
            $accountId_6 => 'accountTitle_6',
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedAccountList_actual = $responder->translateAccountListFormat($accounts);

        $this->assertSame($formattedAccountList_expected, $formattedAccountList_actual);
    }

    /**
     * @test
     */
    public function translateBalanceSheetFormat_FormattedBalanceSheetIsReturned()
    {
        $statements = [
            'asset' => [
                'amount' => 24863926,
                'groups' => [
                    'c46db1a8-daf3-4cd0-befd-2539885d8406' => [
                        'title' => 'GroupTitle1', 'isCurrent' => 1, 'amount' => 267830,
                        'items' => [
                            '12c52f0f-3fd1-4076-bab0-521c6903ebe9' => ['title' => 'ItemTitle11', 'amount' => 218842],
                            '26934102-c58a-42d1-b884-d0cc729d1eeb' => ['title' => 'ItemTitle12', 'amount' => 27000],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'amount' => 357386,
                'groups' => [
                    'e0c81e5c-3b81-4237-837e-e004c2695ee1' => [
                        'title' => 'GroupTitle2', 'isCurrent' => 1, 'amount' => 33235,
                        'items' => [
                            '62cb01ca-fc2f-44f9-a846-43a5a67f7491' => ['title' => 'ItemTitle21', 'amount' => 11187],
                        ],
                    ],
                    'f4d64c3f-991e-4d52-bd10-946587589cac' => [
                        'title' => 'GroupTitle3', 'isCurrent' => 1, 'amount' => 324159,
                        'items' => [
                            'fcadddd5-c5b3-4296-a2cd-81af02db5016' => ['title' => 'ItemTitle31', 'amount' => 22048],
                            'bf9fbc93-f496-4b5c-ab6f-6e0b09f7b20a' => ['title' => 'ItemTitle32', 'amount' => 324151],
                        ],
                    ],
                ],
            ],
            'current_net_asset' => ['amount' => 14379340],
            'net_asset'         => ['amount' => 24506540],
        ];
        $formattedBalanceSheet_expected = [
            [
                'debit'  => ['title' => __('Assets'), 'amount' => '24,863,926', 'bold' => true, 'italic' => true],
                'credit' => ['title' => __('Liabilities'), 'amount' => '357,386', 'bold' => true, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'GroupTitle1', 'amount' => '267,830', 'bold' => false, 'italic' => true],
                'credit' => ['title' => 'GroupTitle2', 'amount' => '33,235', 'bold' => false, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'ItemTitle11', 'amount' => '218,842', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle21', 'amount' => '11,187', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle12', 'amount' => '27,000', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'GroupTitle3', 'amount' => '324,159', 'bold' => false, 'italic' => true],
            ],
            [
                'credit' => ['title' => 'ItemTitle31', 'amount' => '22,048', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => 'ItemTitle32', 'amount' => '324,151', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => __('Current Net Asset'), 'amount' => '14,379,340', 'bold' => true, 'italic' => true],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => __('Net Asset'), 'amount' => '24,506,540', 'bold' => true, 'italic' => true],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedBalanceSheet_actual = $responder->translateBalanceSheetFormat($statements);

        $this->assertSame($formattedBalanceSheet_expected, $formattedBalanceSheet_actual);
    }

    /**
     * @test
     */
    public function translateBalanceSheetFormat_FormattedBalanceSheetIsReturnedWithoutCurrentNetAsset()
    {
        $statements = [
            'asset' => [
                'amount' => 24863926,
                'groups' => [
                    'c46db1a8-daf3-4cd0-befd-2539885d8406' => [
                        'title' => 'GroupTitle1', 'isCurrent' => 1, 'amount' => 267830,
                        'items' => [
                            '12c52f0f-3fd1-4076-bab0-521c6903ebe9' => ['title' => 'ItemTitle11', 'amount' => 218842],
                            '26934102-c58a-42d1-b884-d0cc729d1eeb' => ['title' => 'ItemTitle12', 'amount' => 27000],
                        ],
                    ],
                ],
            ],
            'liability' => [
                'amount' => 357386,
                'groups' => [
                    'e0c81e5c-3b81-4237-837e-e004c2695ee1' => [
                        'title' => 'GroupTitle2', 'isCurrent' => 1, 'amount' => 33235,
                        'items' => [
                            '62cb01ca-fc2f-44f9-a846-43a5a67f7491' => ['title' => 'ItemTitle21', 'amount' => 11187],
                        ],
                    ],
                    'f4d64c3f-991e-4d52-bd10-946587589cac' => [
                        'title' => 'GroupTitle3', 'isCurrent' => 1, 'amount' => 324159,
                        'items' => [
                            'fcadddd5-c5b3-4296-a2cd-81af02db5016' => ['title' => 'ItemTitle31', 'amount' => 22048],
                            'bf9fbc93-f496-4b5c-ab6f-6e0b09f7b20a' => ['title' => 'ItemTitle32', 'amount' => 324151],
                        ],
                    ],
                ],
            ],
            'net_asset'         => ['amount' => 24506540],
        ];
        $formattedBalanceSheet_expected = [
            [
                'debit'  => ['title' => __('Assets'), 'amount' => '24,863,926', 'bold' => true, 'italic' => true],
                'credit' => ['title' => __('Liabilities'), 'amount' => '357,386', 'bold' => true, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'GroupTitle1', 'amount' => '267,830', 'bold' => false, 'italic' => true],
                'credit' => ['title' => 'GroupTitle2', 'amount' => '33,235', 'bold' => false, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'ItemTitle11', 'amount' => '218,842', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle21', 'amount' => '11,187', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle12', 'amount' => '27,000', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'GroupTitle3', 'amount' => '324,159', 'bold' => false, 'italic' => true],
            ],
            [
                'credit' => ['title' => 'ItemTitle31', 'amount' => '22,048', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => 'ItemTitle32', 'amount' => '324,151', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'credit' => ['title' => __('Net Asset'), 'amount' => '24,506,540', 'bold' => true, 'italic' => true],
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedBalanceSheet_actual = $responder->translateBalanceSheetFormat($statements);

        $this->assertSame($formattedBalanceSheet_expected, $formattedBalanceSheet_actual);
    }

    /**
     * @test
     */
    public function translateDraftSlipFormat_FormattedDraftSlipIsReturned()
    {
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
        $slip = [
            $slipId_1 => [
                'date'         => '2019-11-04',
                'slip_outline' => 'slipOutline_4',
                'slip_memo'    => 'slipMemo_4',
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 4210,
                        'client'  => 'client_422',
                        'outline' => 'outline_423',
                    ],
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 4280,
                        'client'  => 'client_429',
                        'outline' => 'outline_430',
                    ],
                    $slipEntryId_3 => [
                        'debit'   => ['account_id' => $accountId_5, 'account_title' => 'accountTitle_5'],
                        'credit'  => ['account_id' => $accountId_6, 'account_title' => 'accountTitle_6'],
                        'amount'  => 4350,
                        'client'  => 'client_436',
                        'outline' => 'outline_437',
                    ],
                ],
            ],
        ];
        $formattedDraftSlip_expected = [
            $slipEntryId_1 => [
                'no'      => substr($slipEntryId_1, 0, 6).'..',
                'debit'   => 'accountTitle_1',
                'client'  => 'client_422',
                'outline' => 'outline_423',
                'credit'  => 'accountTitle_2',
                'amount'  => 4210,
                'trclass' => 'evn',
            ],
            $slipEntryId_2 => [
                'no'      => substr($slipEntryId_2, 0, 6).'..',
                'debit'   => 'accountTitle_3',
                'client'  => 'client_429',
                'outline' => 'outline_430',
                'credit'  => 'accountTitle_4',
                'amount'  => 4280,
                'trclass' => 'odd',
            ],
            $slipEntryId_3 => [
                'no'      => substr($slipEntryId_3, 0, 6).'..',
                'debit'   => 'accountTitle_5',
                'client'  => 'client_436',
                'outline' => 'outline_437',
                'credit'  => 'accountTitle_6',
                'amount'  => 4350,
                'trclass' => 'evn',
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedDraftSlip_actual = $responder->translateDraftSlipFormat($slip);

        $this->assertSame($formattedDraftSlip_expected, $formattedDraftSlip_actual);
    }

    /**
     * @test
     */
    public function translateIncomeStatementFormat_FormattedIncomeStatementIsReturned()
    {
        $statements = [
            'expense' => [
                'amount' => 559319,
                'groups' => [
                    '844db4d1-bdc2-4e1c-a56e-82a166b13afe' => [
                        'title' => 'GroupTitle1', 'isCurrent' => 0, 'amount' => 148112,
                        'items' => [
                            '2bfd6973-2622-49ea-884a-8e18dc15d05e' => ['title' => 'ItemTitle11', 'amount' => 79692],
                            '9a453a41-ba5b-45cc-a29a-d6e12cc08fc0' => ['title' => 'ItemTitle12', 'amount' => 57000],
                            'a4adb10e-ecfc-4497-9501-72813c5e4b54' => ['title' => 'ItemTitle13', 'amount' => 11420],
                        ],
                    ],
                    '1bd92f3a-19d3-4ea2-bc7b-3e791446ddac' => [
                        'title' => 'GroupTitle2', 'isCurrent' => 0, 'amount' => 3610,
                        'items' => [
                            '4bc0d8d9-e8ec-4258-a812-b53c8f7babd7' => ['title' => 'ItemTitle21', 'amount' => 57],
                            '57d3c146-0205-470a-9c3a-409e72432681' => ['title' => 'ItemTitle22', 'amount' => 2457],
                        ],
                    ],
                ],
            ],
            'revenue' => [
                'amount' => 546070,
                'groups' => [
                    'a6b22699-2459-4b62-87d4-2ad422aba824' => [
                        'title' => 'GroupTitle3', 'isCurrent' => 0, 'amount' => 3461,
                        'items' => [
                            '22803ae3-ee3b-4bab-9574-c250bf822c43' => ['title' => 'ItemTitle31', 'amount' => 3457],
                            '8dc19eb8-4707-4d3e-844d-4dd570f768d8' => ['title' => 'ItemTitle32', 'amount' => 4],
                        ],
                    ],
                ],
            ],
            'net_income' => ['amount' => -13249],
        ];
        $formattedIncomeStatement_expected = [
            [
                'debit'  => ['title' => __('Expense'), 'amount' => '559,319', 'bold' => true, 'italic' => true],
                'credit' => ['title' => __('Revenue'), 'amount' => '546,070', 'bold' => true, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'GroupTitle1', 'amount' => '148,112', 'bold' => false, 'italic' => true],
                'credit' => ['title' => 'GroupTitle3', 'amount' => '3,461', 'bold' => false, 'italic' => true],
            ],
            [
                'debit'  => ['title' => 'ItemTitle11', 'amount' => '79,692', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle31', 'amount' => '3,457', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle12', 'amount' => '57,000', 'bold' => false, 'italic' => false],
                'credit' => ['title' => 'ItemTitle32', 'amount' => '4', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle13', 'amount' => '11,420', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'GroupTitle2', 'amount' => '3,610', 'bold' => false, 'italic' => true],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle21', 'amount' => '57', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => 'ItemTitle22', 'amount' => '2,457', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
            [
                'debit'  => ['title' => __('Net Income'), 'amount' => '-13,249', 'bold' => true, 'italic' => true],
                'credit' => ['title' => '', 'amount' => '', 'bold' => false, 'italic' => false],
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $formattedIncomeStatement_actual = $responder->translateIncomeStatementFormat($statements);

        $this->assertSame($formattedIncomeStatement_expected, $formattedIncomeStatement_actual);
    }

    /**
     * @test
     */
    public function translateSlipsFormat_FormattedSlipsAreReturned()
    {
        $slips = [
            '3238ed0a-265a-408d-8e7f-03b5c138abc2' => [
                'date'         => '2019-09-01',
                'slip_outline' => 'slip_outline1',
                'slip_memo'    => null,
                'items'        => [
                    '9d7d0222-ef76-487c-8319-220b2e6911a7' => [
                        'debit' => [
                            'account_id'    => 'dd406a58-d95e-43b6-8d86-b07690ee8983',
                            'account_title' => 'account_title1',
                        ],
                        'credit' => [
                            'account_id'    => 'fcadddd5-c5b3-4296-a2cd-81af02db5016',
                            'account_title' => 'account_title2',
                        ],
                        'amount'  => 1000,
                        'client'  => 'client1',
                        'outline' => 'outline1',
                    ],
                ],
            ],
            'bd9ca022-8d49-4589-9eba-f05ee1af1856' => [
                'date'         => '2019-09-05',
                'slip_outline' => 'slip_outline2',
                'slip_memo'    => null,
                'items'        => [
                    '2847e922-8a45-48f4-97f8-cdeee40ed8f9' => [
                        'debit' => [
                            'account_id'    => '96e7f7d0-e5fa-4946-850d-d3b1138db320',
                            'account_title' => 'account_title3',
                        ],
                        'credit' => [
                            'account_id'    => 'f94c0efc-b82f-4852-b59a-eca5e30fba3c',
                            'account_title' => 'account_title4',
                        ],
                        'amount'  => 50000,
                        'client'  => 'client2',
                        'outline' => 'outline2',
                    ],
                    'e5cade29-f4e4-477b-8e3c-e4ab6ca7caa8' => [
                        'debit' => [
                            'account_id'    => 'ff65f247-427d-4484-a964-6c81588b9a12',
                            'account_title' => 'account_title5',
                        ],
                        'credit' => [
                            'account_id'    => 'f94c0efc-b82f-4852-b59a-eca5e30fba3c',
                            'account_title' => 'account_title4',
                        ],
                        'amount'  => 750,
                        'client'  => 'client3',
                        'outline' => 'outline3',
                    ],
                ],
            ],
        ];
        $slipentryline_expected = [
            [
                'no'      => '9d7d02..',
                'slipno'  => '3238ed..',
                'date'    => '2019-09-01',
                'debit'   => 'account_title1',
                'credit'  => 'account_title2',
                'amount'  => '1,000',
                'client'  => 'client1',
                'outline' => 'outline1',
            ],
            [
                'no'      => '2847e9..',
                'slipno'  => 'bd9ca0..',
                'date'    => '2019-09-05',
                'debit'   => 'account_title3',
                'credit'  => 'account_title4',
                'amount'  => '50,000',
                'client'  => 'client2',
                'outline' => 'outline2',
            ],
            [
                'no'      => 'e5cade..',
                'slipno'  => 'bd9ca0..',
                'date'    => '2019-09-05',
                'debit'   => 'account_title5',
                'credit'  => 'account_title4',
                'amount'  => '750',
                'client'  => 'client3',
                'outline' => 'outline3',
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseViewResponder($ResponseMock, $ViewFactoryMock);
        $slipentryline_actual = $responder->translateSlipsFormat($slips);

        $this->assertSame($slipentryline_expected, $slipentryline_actual);
    }
}
