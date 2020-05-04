<?php

namespace Tests\Unit;

use App\Http\Responder\api\v1\SlipJsonResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_api_v1_SlipJsonResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnJsonResponse()
    {
        $slipId = (string) Str::uuid();
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountId_3 = (string) Str::uuid();
        $accountId_4 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $slipEntryId_2 = (string) Str::uuid();
        $date = '2020-03-30';
        $slip_outline = 'slipOutline_31';
        $slip_memo = 'slipMemo_32';
        $context['slips'] = [
            $slipId => [
                'date'         => $date,
                'slip_outline' => $slip_outline,
                'slip_memo'    => $slip_memo,
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 500,
                        'client'  => 'client_42',
                        'outline' => 'outline_43',
                    ],
                    $slipEntryId_2 => [
                        'debit'   => ['account_id' => $accountId_3, 'account_title' => 'accountTitle_3'],
                        'credit'  => ['account_id' => $accountId_4, 'account_title' => 'accountTitle_4'],
                        'amount'  => 600,
                        'client'  => 'client_50',
                        'outline' => 'outline_51',
                    ],
                ],
            ],
        ];
        $response_body = [
            'id'      => $slipId,
            'date'    => $date,
            'outline' => $slip_outline,
            'memo'    => $slip_memo,
            'entries' => [
                [
                    'id'      => $slipEntryId_1,
                    'debit'   => $accountId_1,
                    'credit'  => $accountId_2,
                    'amount'  => 500,
                    'client'  => 'client_42',
                    'outline' => 'outline_43',
                ],
                [
                    'id'      => $slipEntryId_2,
                    'debit'   => $accountId_3,
                    'credit'  => $accountId_4,
                    'amount'  => 600,
                    'client'  => 'client_50',
                    'outline' => 'outline_51',
                ],
            ],
        ];
        /** @var \Illuminate\Http\JsonResponse|\Mockery\MockInterface $JsonResponseMock */
        $JsonResponseMock = Mockery::mock(JsonResponse::class);
        $JsonResponseMock->shouldReceive('setData')
            ->once()
            ->with($response_body);
        $JsonResponseMock->shouldReceive('setStatusCode')
            ->once()
            ->with(JsonResponse::HTTP_CREATED);

        $responder = new SlipJsonResponder($JsonResponseMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
