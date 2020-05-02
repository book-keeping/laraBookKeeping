<?php

namespace Tests\Unit;

use App\Http\Controllers\api\v1\GetAccountsActionApi;
use App\Http\Responder\api\v1\AccountsJsonResponder;
use App\Service\BookKeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_api_v1_GetAccountsActionApiTest extends TestCase
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
        $context['accounts'] = [
            $accountId_1 => [
                'account_title'       => 'account_title_1',
                'description'         => 'description_1',
                'account_group_id'    => $accountGroupId_1,
                'account_group_title' => 'account_group_title_1',
                'is_current'          => 1,
                'account_type'        => 'asset',
            ],
        ];
        $response_expected = new JsonResponse();
        /** @var \App\Service\BookKeepingService|\Mockery\MockInterface $BookKeepingMock */
        $BookKeepingMock = Mockery::mock(BookKeepingService::class);
        $BookKeepingMock->shouldReceive('retrieveAccountsList')
            ->once()
            ->andReturn($context['accounts']);
        /** @var \App\Http\Responder\api\v1\AccountsJsonResponder|\Mockery\MockInterface $responderMock */
        $responderMock = Mockery::mock(AccountsJsonResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        /** @var \Illuminate\Http\Request|\Mockery\MockInterface $requestMock */
        $requestMock = Mockery::mock(Request::class);

        $controller = new GetAccountsActionApi($BookKeepingMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
