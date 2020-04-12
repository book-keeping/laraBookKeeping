<?php

namespace Tests\Unit;

use App\Http\Responder\Settings\UpdateAccessTokenViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Responder_Settings_UpdateAccessTokenViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function response_ReturnResponseWithToken()
    {
        $context = [
            'token'     => Str::random(60),
            'timestamp' => new Carbon(),
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new UpdateAccessTokenViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function response_ReturnResponseWithoutTimestamp()
    {
        $context = [
            'token'     => null,
            'timestamp' => null,
        ];
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        $ResponseMock->shouldReceive('setContent')->once();
        $ResponseMock->shouldReceive('setStatusCode')->once();
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);
        $ViewFactoryMock->shouldReceive('make')->once();

        $responder = new UpdateAccessTokenViewResponder($ResponseMock, $ViewFactoryMock);
        $response = $responder->response($context);

        $this->assertTrue(true);
    }
}
