<?php

namespace Tests\Unit;

use App\Http\Responder\v1\FindSlipsViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_v1_FindSlipsViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnResponseWithoutSpecifiyingAndOR()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroupId_4 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $context = [
            'accounts' => [
                'asset' => [
                    'groups' => [
                        $accountGroupId_1 => [
                            'isCurrent'    => 0,
                            'bk_code'      => 1200,
                            'createdAt'    => '2020-01-01 12:00:12',
                            'items'        => [
                                $accountId_1 => [
                                    'title'    => 'accountTitle_1',
                                    'bk_code'  => 1201,
                                    'createdAt'=> '2020-01-02 12:00:01',
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
                            'createdAt'    => '2020-01-01 12:00:23',
                            'items'        => [
                                $accountId_2 => [
                                    'title'    => 'accountTitle_2',
                                    'bk_code'  => 2302,
                                    'createdAt'=> '2020-01-02 12:00:02',
                                ],
                            ],
                        ],
                    ],
                ],
                'expense' => [
                    'groups' => [
                        $accountGroupId_3 => [
                            'isCurrent'    => 0,
                            'bk_code'      => 2400,
                            'createdAt'    => '2020-01-01 12:00:24',
                            'items'        => [
                                $accountId_3 => [
                                    'title'    => 'accountTitle_3',
                                    'bk_code'  => 2403,
                                    'createdAt'=> '2020-01-02 12:00:04',
                                ],
                            ],
                        ],
                    ],
                ],
                'revenue' => [
                    'groups' => [
                        $accountGroupId_4 => [
                            'isCurrent'    => 1,
                            'bk_code'      => 5100,
                            'createdAt'    => '2020-01-01 12:00:51',
                            'items'        => [
                                $accountId_4 => [
                                    'title'    => 'accountTitle_4',
                                    'bk_code'  => 5104,
                                    'createdAt'=> '2020-01-02 12:00:06',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'and_or'         => null,
            'beginning_date' => '2020-03-01',
            'end_date'       => '2020-03-02',
            'debit'          => $accountId_1,
            'credit'         => $accountId_2,
            'keyword'        => 'keyword',
            'slips'          => [
                $slipId_1 => [
                    'date'         => '2020-02-10',
                    'slip_outline' => 'slipOutline_10',
                    'slip_memo'    => 'slipMemo_10',
                    'items'        => [
                        $slipEntryId_1 => [
                            'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                            'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                            'amount'  => 1210,
                            'client'  => 'client_122',
                            'outline' => 'outline_123',
                        ],
                        $slipEntryId_2 => [
                            'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                            'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                            'amount'  => 1280,
                            'client'  => 'client_129',
                            'outline' => 'outline_130',
                        ],
                    ],
                ],
            ],
            'message' => 'message',
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new FindSlipsViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function response_ReturnResponseWithSpecifiyingAndOR()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $context = [
            'accounts' => [
                'asset' => [
                    'groups' => [],
                ],
                'liability' => [
                    'groups' => [],
                ],
                'expense' => [
                    'groups' => [],
                ],
                'revenue' => [
                    'groups' => [],
                ],
            ],
            'and_or'         => 'or',
            'beginning_date' => null,
            'end_date'       => null,
            'debit'          => $accountId_1,
            'credit'         => $accountId_2,
            'keyword'        => null,
            'slips'          => [],
            'message'        => null,
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new FindSlipsViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
