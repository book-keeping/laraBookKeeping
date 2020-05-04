<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\PostSlipsActionApi;
use App\Http\Responder\api\v1\SlipJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class Http_Controllers_api_v1_PostSlipsActionApiTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseForRequestedDate()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $slipId_1 = (string) Str::uuid();
        $slipEntryId_1 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
        ];
        $slip = [
            'outline' => 'outline32',
            'date'    => '2020-01-01',
            'entries' => [
                [
                    'debit'   => $accountId_1,
                    'credit'  => $accountId_2,
                    'amount'  => 380,
                    'client'  => 'client39',
                    'outline' => 'outline40',
                ],
            ],
            'memo' => 'memo43',
        ];
        $context['slips'] = [
            $slipId_1 => [
                'date'         => '2020-01-01',
                'slip_outline' => 'outline32',
                'slip_memo'    => 'memo43',
                'items'        => [
                    $slipEntryId_1 => [
                        'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                        'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                        'amount'  => 380,
                        'client'  => 'client39',
                        'outline' => 'outline40',
                    ],
                ],
            ],
        ];
        $response_expected = new JsonResponse();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsList')
            ->once()
            ->andReturn($accounts);
        $BookKeepingMock->shouldReceive('createSlip')
            ->once()
            ->with($slip['outline'], $slip['date'], $slip['entries'], $slip['memo'])
            ->andReturn($slipId_1);
        $BookKeepingMock->shouldReceive('retrieveSlip')
            ->once()
            ->with($slipId_1)
            ->andReturn($context['slips']);
        $BookKeepingMock->shouldReceive('validateDateFormat')
            ->once()
            ->with($slip['date'])
            ->andReturn(true);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn($slip);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseWithNoDateRequest()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accounts = [
            $accountId_1 => ['account_title' => 'accountTitle_1'],
            $accountId_2 => ['account_title' => 'accountTitle_2'],
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsList')
            ->once()
            ->andReturn($accounts);
        $BookKeepingMock->shouldNotReceive('createSlip');
        $BookKeepingMock->shouldNotReceive('retrieveSlip');
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);
        $responderMock->shouldNotReceive('response');
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('all')
            ->once()
            ->andReturn([]);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame(JsonResponse::HTTP_BAD_REQUEST, $response_actual->getStatusCode());
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimAccounts
     */
    public function validateAndTrimAccounts_MachValidationResult($array_in, $key, $string_expected)
    {
        $accounts = [
            '3274cc74-f7ab-40a4-984a-186a593401f7' => null,
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimAccounts');
        $method->setAccessible(true);
        $string_actual = $method->invoke($controller, $array_in, $key, $accounts);

        $this->assertSame($string_expected, $string_actual);
    }

    public function forValidateAndTrimAccounts()
    {
        return [
            [['debit' => 0], 'debit', null],
            [['debit' => '  471b26d0-99a1-47f4-aa57-2722f6011f2a'], 'debit', null],
            [['debit' => '3274cc74-f7ab-40a4-984a-186a593401f7  '], 'debit', '3274cc74-f7ab-40a4-984a-186a593401f7'],
        ];
    }

    /**
     * @test
     * @dataProvider forTestTrimDraftSlip
     */
    public function validateAndTrimDraftSlip_MachValidationResult($slip_in, $result_expected, $callValidateDateFormat, $validateDateFormatResult)
    {
        $accounts = [
            '3274cc74-f7ab-40a4-984a-186a593401f7' => null,
            '471b26d0-99a1-47f4-aa57-2722f6011f2a' => null,
            '8548f227-7be9-42be-b5c7-66da432f5dad' => null,
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        if ($callValidateDateFormat) {
            $BookKeepingMock->shouldReceive('validateDateFormat')
                ->once()
                ->with(trim($slip_in['date']))
                ->andReturn($validateDateFormatResult);
        }
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimDraftSlip');
        $method->setAccessible(true);
        $result_actual = $method->invoke($controller, $slip_in, $accounts);

        $this->assertSame($result_expected, $result_actual);
    }

    public function forTestTrimDraftSlip()
    {
        return [
            [
                [],
                [
                    'success' => false,
                    'slip'    => ['memo' => null],
                ],
                false,
                false,
            ],
            [
                [
                    'outline' => ['outline'],
                    'date'    => ['2020-01-02'],
                    'entries' => [],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => [],
                ],
                false,
                false,
            ],
            [
                [
                    'outline' => '   outline339',
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['outline' => 'outline339'],
                ],
                false,
                false,
            ],
            [
                [
                    'date' => '   2020-01-32',
                    'memo' => [],
                ],
                [
                    'success' => false,
                    'slip'    => [],
                ],
                true,
                false,
            ],
            [
                [
                    'date' => '   2020-01-20',
                    'memo' => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['date' => '2020-01-20'],
                ],
                true,
                true,
            ],
            [
                [
                    'entries' => 'entries1',
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => [],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => [[]],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => []],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => ['entries1'],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => []],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => [['debit' => '   3274cc74-f7ab-40a4-984a-186a593401f7']],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => [['debit' => '3274cc74-f7ab-40a4-984a-186a593401f7']]],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => [['debit' => '   90dc7df5-07ea-4086-9461-0555c2a9d03c']],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => []],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => [['debit' => '   3274cc74-f7ab-40a4-984a-186a593401f7']],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => [['debit' => '3274cc74-f7ab-40a4-984a-186a593401f7']]],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => [[], ['debit' => '   3274cc74-f7ab-40a4-984a-186a593401f7']],
                    'memo'    => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => [['debit' => '3274cc74-f7ab-40a4-984a-186a593401f7']]],
                ],
                false,
                false,
            ],
            [
                [
                    'entries' => [
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                            'amount'  => 4630,
                            'client'  => ' client464',
                            'outline' => 'outline465 ',
                        ],
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7  ',
                            'credit'  => '8548f227-7be9-42be-b5c7-66da432f5dad    ',
                            'amount'  => 4700,
                            'client'  => 'client471 ',
                            'outline' => ' outline472',
                        ],
                    ],
                    'memo' => [],
                ],
                [
                    'success' => false,
                    'slip'    => ['entries' => [
                        [
                            'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                            'amount'  => 4630,
                            'client'  => 'client464',
                            'outline' => 'outline465',
                        ],
                        [
                            'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '8548f227-7be9-42be-b5c7-66da432f5dad',
                            'amount'  => 4700,
                            'client'  => 'client471',
                            'outline' => 'outline472',
                        ],
                    ]],
                ],
                false,
                false,
            ],
            [
                [
                    'outline' => '   outline501',
                    'date'    => '   2020-03-31',
                    'entries' => [
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                            'amount'  => 5070,
                            'client'  => ' client508',
                            'outline' => 'outline509 ',
                        ],
                    ],
                    'memo' => 'memo1',
                ],
                [
                    'success' => true,
                    'slip'    => [
                        'outline' => 'outline501',
                        'date'    => '2020-03-31',
                        'entries' => [
                            [
                                'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                                'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                                'amount'  => 5070,
                                'client'  => 'client508',
                                'outline' => 'outline509',
                            ],
                        ],
                        'memo' => 'memo1',
                    ],
                ],
                true,
                true,
            ],
            [
                [
                    'outline' => '   outline536',
                    'date'    => '   2020-02-28',
                    'entries' => [
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                            'amount'  => 5420,
                            'client'  => ' client543',
                            'outline' => 'outline544 ',
                        ],
                    ],
                ],
                [
                    'success' => true,
                    'slip'    => [
                        'outline' => 'outline536',
                        'date'    => '2020-02-28',
                        'entries' => [
                            [
                                'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                                'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                                'amount'  => 5420,
                                'client'  => 'client543',
                                'outline' => 'outline544',
                            ],
                        ],
                        'memo' => null,
                    ],
                ],
                true,
                true,
            ],
            [
                [
                    'outline' => '   outline570',
                    'date'    => '   2019-12-31',
                    'entries' => [
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                            'amount'  => 5760,
                            'client'  => ' client577',
                            'outline' => 'outline578 ',
                        ],
                    ],
                    'memo' => null,
                ],
                [
                    'success' => true,
                    'slip'    => [
                        'outline' => 'outline570',
                        'date'    => '2019-12-31',
                        'entries' => [
                            [
                                'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                                'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                                'amount'  => 5760,
                                'client'  => 'client577',
                                'outline' => 'outline578',
                            ],
                        ],
                        'memo' => null,
                    ],
                ],
                true,
                true,
            ],
            [
                [
                    'outline' => '   outline605',
                    'date'    => '   2019-11-30',
                    'entries' => [
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                            'amount'  => 6110,
                            'client'  => ' client612',
                            'outline' => 'outline613 ',
                        ],
                    ],
                    'memo' => ['memo1'],
                ],
                [
                    'success' => false,
                    'slip'    => [
                        'outline' => 'outline605',
                        'date'    => '2019-11-30',
                        'entries' => [
                            [
                                'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                                'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                                'amount'  => 6110,
                                'client'  => 'client612',
                                'outline' => 'outline613',
                            ],
                        ],
                    ],
                ],
                true,
                true,
            ],
            [
                [
                    'outline' => '   outline539',
                    'date'    => '   2019-10-31',
                    'entries' => [
                        [
                            'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                            'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                            'amount'  => 6450,
                            'client'  => ' client646',
                            'outline' => 'outline647 ',
                        ],
                    ],
                    'memo' => '  ',
                ],
                [
                    'success' => true,
                    'slip'    => [
                        'outline' => 'outline539',
                        'date'    => '2019-10-31',
                        'entries' => [
                            [
                                'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                                'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                                'amount'  => 6450,
                                'client'  => 'client646',
                                'outline' => 'outline647',
                            ],
                        ],
                        'memo' => null,
                    ],
                ],
                true,
                true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimDraftSlipEntry
     */
    public function validateAndTrimDraftSlipEntry_MachValidationResult($slipEntry_in, $result_expected)
    {
        $accounts = [
            '3274cc74-f7ab-40a4-984a-186a593401f7' => null,
            '471b26d0-99a1-47f4-aa57-2722f6011f2a' => null,
        ];
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimDraftSlipEntry');
        $method->setAccessible(true);
        $result_actual = $method->invoke($controller, $slipEntry_in, $accounts);

        $this->assertSame($result_expected, $result_actual);
    }

    public function forValidateAndTrimDraftSlipEntry()
    {
        return [
            [
                [
                    'debit'   => '8548f227-7be9-42be-b5c7-66da432f5dad',
                    'credit'  => '90dc7df5-07ea-4086-9461-0555c2a9d03c',
                    'client'  => ['client122'],
                    'outline' => ['outline123'],
                ],
                [
                    'success'   => false,
                    'slipEntry' => [],
                ],
            ],
            [
                [
                    'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7  ',
                    'credit'  => '90dc7df5-07ea-4086-9461-0555c2a9d03c',
                    'client'  => ['client134'],
                    'outline' => ['outline135'],
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'debit' => '3274cc74-f7ab-40a4-984a-186a593401f7',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '8548f227-7be9-42be-b5c7-66da432f5dad',
                    'credit'  => '  471b26d0-99a1-47f4-aa57-2722f6011f2a',
                    'client'  => ['client148'],
                    'outline' => ['outline149'],
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'credit' => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '8548f227-7be9-42be-b5c7-66da432f5dad',
                    'credit'  => '90dc7df5-07ea-4086-9461-0555c2a9d03c',
                    'client'  => '  client162',
                    'outline' => ['outline163'],
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'client' => 'client162',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '8548f227-7be9-42be-b5c7-66da432f5dad',
                    'credit'  => '90dc7df5-07ea-4086-9461-0555c2a9d03c',
                    'client'  => ['client176'],
                    'outline' => 'outline177  ',
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'outline' => 'outline177',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '  3274cc74-f7ab-40a4-984a-186a593401f7',
                    'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a  ',
                    'amount'  => 1900,
                    'client'  => ' client191 ',
                    'outline' => '  outline192  ',
                ],
                [
                    'success'   => true,
                    'slipEntry' => [
                        'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                        'amount'  => 1900,
                        'client'  => 'client191',
                        'outline' => 'outline192',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                    'credit'  => '3274cc74-f7ab-40a4-984a-186a593401f7',
                    'amount'  => 2090,
                    'client'  => 'client210',
                    'outline' => 'outline211',
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit'  => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'amount'  => 2090,
                        'client'  => 'client210',
                        'outline' => 'outline211',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                    'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                    'client'  => 'client228',
                    'outline' => 'outline229',
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                        'client'  => 'client228',
                        'outline' => 'outline229',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                    'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                    'amount'  => 0,
                    'client'  => 'client246',
                    'outline' => 'outline247',
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                        'client'  => 'client246',
                        'outline' => 'outline247',
                    ],
                ],
            ],
            [
                [
                    'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                    'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                    'amount'  => '123',
                    'client'  => 'client264',
                    'outline' => 'outline265',
                ],
                [
                    'success'   => false,
                    'slipEntry' => [
                        'debit'   => '3274cc74-f7ab-40a4-984a-186a593401f7',
                        'credit'  => '471b26d0-99a1-47f4-aa57-2722f6011f2a',
                        'client'  => 'client264',
                        'outline' => 'outline265',
                    ],
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider forValidateAndTrimString
     */
    public function validateAndTrimString_MachValidationResult($array_in, $key, $string_expected)
    {
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        /** @var \App\Http\Responder\api\v1\SlipJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(SlipJsonResponder::class);

        $controller = new PostSlipsActionApi($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateAndTrimString');
        $method->setAccessible(true);
        $string_actual = $method->invoke($controller, $array_in, $key);

        $this->assertSame($string_expected, $string_actual);
    }

    public function forValidateAndTrimString()
    {
        return [
            [[], 'outline', null],
            [['outline' => ['  outline53']], 'outline', null],
            [['outline' => '  '], 'outline', null],
            [['outline' => '  outline55'], 'outline', 'outline55'],
        ];
    }
}
