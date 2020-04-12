<?php

namespace Tests\Unit;

use App\Http\Controllers\v1\CreateSlipActionHTML;
use App\Http\Responder\v1\CreateSlipViewResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use ReflectionClass;
use Tests\TestCase;

class Http_Controllers_v1_CreateSlipActionHTMLTest extends TestCase
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
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $context = [
            'accounts' => [
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
                                $accountId_2 => [
                                    'title'    => 'accountTitle_2',
                                    'bk_code'  => 1202,
                                    'createdAt'=> '2019-12-03 12:00:01',
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
            'slipdate'    => '2020-01-02',
            'draftslip'   => [],
            'totalamount' => 0,
        ];
        $response_expected = new Response();
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsForSelect')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn($context['draftslip']);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn('');
        Carbon::setTestNow(new Carbon('2020-01-02 09:59:59'));

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForAddRequest()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
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
                            'createdAt'    => '2019-12-01 12:00:12',
                            'items'        => [
                                $accountId_1 => [
                                    'title'    => 'accountTitle_1',
                                    'bk_code'  => 1201,
                                    'createdAt'=> '2019-12-02 12:00:01',
                                ],
                                $accountId_2 => [
                                    'title'    => 'accountTitle_2',
                                    'bk_code'  => 1202,
                                    'createdAt'=> '2019-12-03 12:00:01',
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
            'add' => [
                'debit'   => $accountId_1,
                'client'  => 'client_103',
                'outline' => 'outline_104',
                'credit'  => $accountId_2,
                'amount'  => 1450,
            ],
            'slipdate'  => '2020-01-03',
            'draftslip' => [
                $slipId_1 => [
                    'date'         => '2020-01-03',
                    'slip_outline' => 'slipOutline_15',
                    'slip_memo'    => 'slipMemo_15',
                    'items'        => [
                        $slipEntryId_1 => [
                            'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                            'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                            'amount'  => 1450,
                            'client'  => 'client_103',
                            'outline' => 'outline_104',
                        ],
                        $slipEntryId_2 => [
                            'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                            'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                            'amount'  => 1670,
                            'client'  => 'client_168',
                            'outline' => 'outline_169',
                        ],
                    ],
                ],
            ],
            'totalamount' => 3120,
        ];
        $response_expected = new Response();
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsForSelect')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldReceive('createSlipEntryAsDraft')
            ->once()
            ->with(
                $context['add']['debit'],
                $context['add']['client'],
                $context['add']['outline'],
                $context['add']['credit'],
                $context['add']['amount'],
            );
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        $BookKeepingMock->shouldNotReceive('submitDraftSlip');
        $BookKeepingMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn($context['draftslip']);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn('');
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['add' => 'Add']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('debit')
            ->andReturn($context['add']['debit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('client')
            ->andReturn($context['add']['client']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('outline')
            ->andReturn($context['add']['outline']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('credit')
            ->andReturn($context['add']['credit']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('amount')
            ->andReturn($context['add']['amount']);
        Carbon::setTestNow(new Carbon('2020-01-03 09:59:59'));

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
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
        $accountGroupId_1 = (string) Str::uuid();
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
                            'createdAt'    => '2019-12-01 12:00:12',
                            'items'        => [
                                $accountId_1 => [
                                    'title'    => 'accountTitle_1',
                                    'bk_code'  => 1201,
                                    'createdAt'=> '2019-12-02 12:00:01',
                                ],
                                $accountId_2 => [
                                    'title'    => 'accountTitle_2',
                                    'bk_code'  => 1202,
                                    'createdAt'=> '2019-12-03 12:00:01',
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
            'slipdate'  => '2020-01-03',
            'draftslip' => [
                $slipId_1 => [
                    'date'         => '2020-01-03',
                    'slip_outline' => 'slipOutline_16',
                    'slip_memo'    => 'slipMemo_16',
                    'items'        => [
                        $slipEntryId_2 => [
                            'debit'   => ['account_id' => $accountId_1, 'account_title' => 'accountTitle_1'],
                            'credit'  => ['account_id' => $accountId_2, 'account_title' => 'accountTitle_2'],
                            'amount'  => 2950,
                            'client'  => 'client_296',
                            'outline' => 'outline_297',
                        ],
                    ],
                ],
            ],
            'totalamount' => 2950,
        ];
        $response_expected = new Response();
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsForSelect')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldNotReceive('createSlipEntryAsDraft');
        $BookKeepingMock->shouldReceive('deleteSlipEntryAsDraft')
            ->once()
            ->with($slipEntryId_1);
        $BookKeepingMock->shouldNotReceive('submitDraftSlip');
        $BookKeepingMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn($context['draftslip']);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn($context['slipdate']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['delete' => 'Delete']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('modifyno')
            ->andReturn($slipEntryId_1);

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTForSubmitRequest()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $context = [
            'accounts' => [
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
                                $accountId_2 => [
                                    'title'    => 'accountTitle_2',
                                    'bk_code'  => 1202,
                                    'createdAt'=> '2019-12-03 12:00:01',
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
            'slipdate'    => '2020-01-04',
            'draftslip'   => [],
            'totalamount' => 0,
        ];
        $response_expected = new Response();
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsForSelect')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldNotReceive('createSlipEntryAsDraft');
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        $BookKeepingMock->shouldReceive('submitDraftSlip')
            ->once()
            ->with($context['slipdate']);
        $BookKeepingMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn($context['draftslip']);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn($context['slipdate']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['submit' => 'Submit']);

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_MakeContextAndReturnResponseToHandlePOSTWithNoRequest()
    {
        $accountId_1 = (string) Str::uuid();
        $accountId_2 = (string) Str::uuid();
        $accountGroupId_1 = (string) Str::uuid();
        $context = [
            'accounts' => [
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
                                $accountId_2 => [
                                    'title'    => 'accountTitle_2',
                                    'bk_code'  => 1202,
                                    'createdAt'=> '2019-12-03 12:00:01',
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
            'slipdate'    => '2020-01-04',
            'draftslip'   => [],
            'totalamount' => 0,
        ];
        $response_expected = new Response();
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsForSelect')
            ->once()
            ->andReturn($context['accounts']);
        $BookKeepingMock->shouldNotReceive('createSlipEntryAsDraft');
        $BookKeepingMock->shouldNotReceive('deleteSlipEntryAsDraft');
        $BookKeepingMock->shouldNotReceive('submitDraftSlip');
        $BookKeepingMock->shouldReceive('retrieveDraftSlips')
            ->once()
            ->andReturn($context['draftslip']);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('date')
            ->andReturn($context['slipdate']);
        $requestMock->shouldReceive('input')
            ->once()
            ->with('buttons')
            ->andReturn(['other' => 'Othere']);

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     * @dataProvider forTestValidateDateFormat
     */
    public function validateDateFormat($date, $success_expected)
    {
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateDateFormat');
        $method->setAccessible(true);
        $success_actual = $method->invoke($controller, $date);

        $this->assertSame($success_expected, $success_actual);
    }

    public function forTestValidateDateFormat()
    {
        return [
            ['2019-01-32', false],
            ['2019-02-29', false],
            ['2020-02-29', true],
            ['2020-04-31', false],
            ['2020-03-01', true],
            ['2020/03/01', false],
            ['20200301',   false],
            ['2020-03-1',  false],
            ['2020-3-1',   false],
            ['2020-3-01',  false],
        ];
    }

    /**
     * @test
     * @dataProvider forTestValidateForCreateSlipEntry
     */
    public function validateForCreateSlipEntry($debit, $client, $outline, $credit, $amount, $success_expected)
    {
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $responderMock = Mockery::mock(CreateSlipViewResponder::class);

        $controller = new CreateSlipActionHTML($BookKeepingMock, $responderMock);
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod('validateForCreateSlipEntry');
        $method->setAccessible(true);
        $success_actual = $method->invoke($controller, $debit, $client, $outline, $credit, $amount);

        $this->assertSame($success_expected, $success_actual);
    }

    public function forTestValidateForCreateSlipEntry()
    {
        return [
            ['6eb9015e-039f-4028-8b2b-3f3279c8849c', 'client', 'outline', 'f4c7b564-79e4-4037-9814-ec4bb040c58e', 1234, true],
            ['0',                                    'client', 'outline', 'f4c7b564-79e4-4037-9814-ec4bb040c58e', 1234, false],
            ['6eb9015e-039f-4028-8b2b-3f3279c8849c', '',       'outline', 'f4c7b564-79e4-4037-9814-ec4bb040c58e', 1234, false],
            ['6eb9015e-039f-4028-8b2b-3f3279c8849c', 'client', '',        'f4c7b564-79e4-4037-9814-ec4bb040c58e', 1234, false],
            ['6eb9015e-039f-4028-8b2b-3f3279c8849c', 'client', 'outline', '0',                                    1234, false],
            ['6eb9015e-039f-4028-8b2b-3f3279c8849c', 'client', 'outline', 'f4c7b564-79e4-4037-9814-ec4bb040c58e',    0, false],
            ['6eb9015e-039f-4028-8b2b-3f3279c8849c', 'client', 'outline', '6eb9015e-039f-4028-8b2b-3f3279c8849c', 1234, false],
        ];
    }
}
