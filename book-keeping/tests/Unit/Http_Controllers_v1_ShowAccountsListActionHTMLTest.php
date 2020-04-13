<?php

namespace Tests\Unit;

use App\Http\Controllers\v1\ShowAccountsListActionHTML;
use App\Http\Responder\v1\ShowAccountsListViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v1_ShowAccountsListActionHTMLTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponse()
    {
        $accountId_1 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
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
                    'groups' => [],
                ],
                'expense' => [
                    'groups' => [],
                ],
                'revenue' => [
                    'groups' => [],
                ],
            ],
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v1\ShowAccountsListViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowAccountsListViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowAccountsListActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
