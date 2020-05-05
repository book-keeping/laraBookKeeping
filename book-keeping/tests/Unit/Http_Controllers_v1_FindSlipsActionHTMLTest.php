<?php

namespace Tests\Unit;

use App\Http\Controllers\v1\FindSlipsActionHTML;
use App\Http\Responder\v1\FindSlipsViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v1_FindSlipsActionHTMLTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        Carbon::setTestNow();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandleGET()
    {
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
            'beginning_date' => null,
            'end_date'       => null,
            'debit'          => null,
            'credit'         => null,
            'and_or'         => null,
            'keyword'        => null,
            'slips'          => null,
            'message'        => __('There is no condition for search.'),
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);

        $controller = new FindSlipsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForDeleteRequest()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
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
            'beginning_date' => null,
            'end_date'       => null,
            'debit'          => $accountId_1,
            'credit'         => $accountId_2,
            'and_or'         => 'and',
            'keyword'        => null,
            'slips'          => [],
            'message'        => __('No items that match the condition.'),
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldReceive('deleteSlipEntryAsDraft')
            ->once()
            ->andReturn($slipEntryId_1);
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with($context['beginning_date'], $context['end_date'])
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->andReturn([]);
        /** @var \App\Http\Responder\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['delete' => 'Delete']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('modifyno')
            ->andReturn([$slipEntryId_1]);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('debit')
            ->andReturn($context['debit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('credit')
            ->andReturn($context['credit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('ANDOR')
            ->andReturn($context['and_or']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('KEYWORD')
            ->andReturn(null);

        $controller = new FindSlipsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForSearchRequest()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
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
            'beginning_date' => null,
            'end_date'       => null,
            'debit'          => $accountId_1,
            'credit'         => $accountId_2,
            'and_or'         => 'and',
            'keyword'        => null,
            'slips'          => [
                $slipId_1 => [
                    'date'         => '2020-01-03',
                    'slip_outline' => 'slipOutline_15',
                    'slip_memo'    => 'slipMemo_15',
                    'items'        => [
                        $slipEntryId_2 => [
                            'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                            'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                            'amount'  => 1670,
                            'client'  => 'client_118',
                            'outline' => 'outline_119',
                        ],
                    ],
                ],
            ],

            'message' => null,
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with($context['beginning_date'], $context['end_date'])
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['search' => 'Search']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('modifyno')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('debit')
            ->andReturn($context['debit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('credit')
            ->andReturn($context['credit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('ANDOR')
            ->andReturn($context['and_or']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('KEYWORD')
            ->andReturn(null);

        $controller = new FindSlipsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForSearchRequestWithoutConditions()
    {
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
            'beginning_date' => null,
            'end_date'       => null,
            'debit'          => '0',
            'credit'         => '0',
            'and_or'         => '',
            'keyword'        => null,
            'slips'          => null,
            'message'        => __('There is no condition for search.'),
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        $BookKeepingMock->shouldNotReceive('validatePeriod');
        $BookKeepingMock->shouldNotReceive('retrieveSlips');
        /** @var \App\Http\Responder\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['search' => 'Search']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('modifyno')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('debit')
            ->andReturn('0');
        $requestMock->shouldReceive('input')
            ->once()
            ->with('credit')
            ->andReturn('0');
        $requestMock->shouldReceive('input')
            ->once()
            ->with('ANDOR')
            ->andReturn('');
        $requestMock->shouldReceive('input')
            ->once()
            ->with('KEYWORD')
            ->andReturn(null);

        $controller = new FindSlipsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForSearchRequestWithoutReturnedSlips()
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
            'beginning_date' => null,
            'end_date'       => null,
            'debit'          => $accountId_1,
            'credit'         => $accountId_2,
            'and_or'         => 'and',
            'keyword'        => null,
            'slips'          => [],
            'message'        => __('No items that match the condition.'),
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccounts')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        $BookKeepingMock->shouldReceive('validatePeriod')
            ->once()
            ->with($context['beginning_date'], $context['end_date'])
            ->andReturn(true);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->andReturn([]);
        /** @var \App\Http\Responder\v1\FindSlipsViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(FindSlipsViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['search' => 'Search']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('modifyno')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('BEGINNING')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('END')
            ->andReturn(null);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('debit')
            ->andReturn($context['debit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('credit')
            ->andReturn($context['credit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('ANDOR')
            ->andReturn($context['and_or']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('KEYWORD')
            ->andReturn(null);

        $controller = new FindSlipsActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
