<?php

namespace Tests\Unit;

use App\Http\Responder\api\v1\AccountsJsonResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_api_v1_AccountsJsonResponderTest extends TestCase
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
        $response_body = [
            [
                'id'          => $accountId_1,
                'title'       => 'account_title_1',
                'description' => 'description_1',
                'group'       => $accountGroupId_1,
                'group_title' => 'account_group_title_1',
                'is_current'  => 1,
                'type'        => 'asset',
            ],
        ];
        /** @var \Illuminate\Http\JsonResponse|\Mockery\MockInterface $JsonResponseMock */
        $JsonResponseMock = Mockery::mock(JsonResponse::class);
        $JsonResponseMock->shouldReceive('setData')
            ->once()
            ->with($response_body);
        $JsonResponseMock->shouldReceive('setStatusCode')
            ->once()
            ->with(JsonResponse::HTTP_OK);

        $responder = new AccountsJsonResponder($JsonResponseMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
