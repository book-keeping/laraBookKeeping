<?php

namespace Tests\Unit;

use App\Http\Responder\v1\ShowAccountsListViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_v1_ShowAccountsListViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnResponse()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $accountGroupId_2 = (string) Str::uuid();
        $accountGroupId_3 = (string) Str::uuid();
        $accountGroupId_4 = (string) Str::uuid();
        $context = [
            'accounts' => [
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
                    'groups' => [
                        $accountGroupId_3 => [
                            'title'     => 'accountGroupTitle_3',
                            'isCurrent' => 0,
                            'bk_code'   => 2400,
                            'createdAt' => '2019-12-01 12:00:24',
                            'items'     => [
                                $accountId_3 => [
                                    'title'       => 'accountTitle_3',
                                    'description' => 'description_3',
                                    'bk_code'     => 2403,
                                    'createdAt'   => '2019-12-02 12:00:04',
                                ],
                            ],
                        ],
                    ],
                ],
                'revenue' => [
                    'groups' => [
                        $accountGroupId_4 => [
                            'title'     => 'accountGroupTitle_4',
                            'isCurrent' => 1,
                            'bk_code'   => 5100,
                            'createdAt' => '2019-12-01 12:00:51',
                            'items'     => [
                                $accountId_4 => [
                                    'title'       => 'accountTitle_4',
                                    'description' => 'description_4',
                                    'bk_code'     => 5104,
                                    'createdAt'   => '2019-12-02 12:00:06',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new ShowAccountsListViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
