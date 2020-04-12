<?php

namespace Tests\Unit;

use App\Http\Controllers\v1\ShowTopActionHTML;
use App\Http\Responder\v1\ShowTopViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Controllers_v1_ShowTopActionHTMLTest extends TestCase
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
        $today = date('Y-m-d');
        $context = [
            'date'             => $today,
            'income_statement' => [
                'income_statement_item1' => ['amount' => 11, 'groups' => []],
                'income_statement_item2' => ['amount' => 12, 'groups' => []],
                'income_statement_item3' => ['amount' => 13, 'groups' => []],
            ],
            'balance_sheet' => [
                'balance_sheet_itme1' => ['amount' => 11, 'groups' => []],
                'balance_sheet_itme2' => ['amount' => 21, 'groups' => []],
                'balance_sheet_itme3' => ['amount' => 31, 'groups' => []],
            ],
            'slips' => [
                'slips_itme1' => [
                    'date'         => 'date1',
                    'slip_outline' => 'slip_outline1',
                    'slip_memo'    => 'slip_memo1',
                    'items'        => [],
                ],
            ],
        ];
        $response_expected = new Response();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with($today, $today)
            ->andReturn($context['income_statement']);
        $BookKeepingMock->shouldReceive('retrieveStatements')
            ->once()
            ->with('1970-01-01', $today)
            ->andReturn($context['balance_sheet']);
        $BookKeepingMock->shouldReceive('retrieveSlips')
            ->once()
            ->with($today, $today)
            ->andReturn($context['slips']);
        /** @var \App\Http\Responder\v1\ShowTopViewResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(ShowTopViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new ShowTopActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
