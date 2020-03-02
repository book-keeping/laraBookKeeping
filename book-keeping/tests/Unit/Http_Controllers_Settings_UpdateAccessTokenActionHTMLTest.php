<?php

namespace Tests\Unit;

use App\Http\Controllers\Settings\UpdateAccessTokenActionHTML;
use App\Http\Responder\Settings\UpdateAccessTokenViewResponder;
use App\Service\AccessTokenService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Http_Controllers_Settings_UpdateAccessTokenActionHTMLTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function __invoke_DeleteTokenForDELETERequest()
    {
        $timestamp = new Carbon();
        $user = new User();
        $context = [
            'token'     => null,
            'timestamp' => $timestamp,
        ];
        $response_expected = new Response();
        $accessTokenMock = Mockery::mock(AccessTokenService::class);
        $accessTokenMock->shouldReceive('setUser')
            ->once()
            ->with($user);
        $accessTokenMock->shouldNotReceive('generate');
        $accessTokenMock->shouldReceive('delete')
            ->once();
        $accessTokenMock->shouldReceive('createdAt')
            ->once()
            ->andReturn($timestamp);
        $responderMock = Mockery::mock(UpdateAccessTokenViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('user')
            ->once()
            ->andReturn($user);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('delete')
            ->andReturn(true);

        $controller = new UpdateAccessTokenActionHTML($accessTokenMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_GenerateTokenForPOSTRequest()
    {
        $token = Str::random(60);
        $timestamp = new Carbon();
        $user = new User();
        $context = [
            'token'     => $token,
            'timestamp' => $timestamp,
        ];
        $response_expected = new Response();
        $accessTokenMock = Mockery::mock(AccessTokenService::class);
        $accessTokenMock->shouldReceive('setUser')
            ->once()
            ->with($user);
        $accessTokenMock->shouldReceive('generate')
            ->once()
            ->andReturn($token);
        $accessTokenMock->shouldNotReceive('delete');
        $accessTokenMock->shouldReceive('createdAt')
            ->once()
            ->andReturn($timestamp);
        $responderMock = Mockery::mock(UpdateAccessTokenViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('user')
            ->once()
            ->andReturn($user);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(true);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('delete')
            ->andReturn(false);

        $controller = new UpdateAccessTokenActionHTML($accessTokenMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }

    /**
     * @test
     */
    public function __invoke_RetriveTimestampOfTokenForGETRequest()
    {
        $timestamp = new Carbon();
        $user = new User();
        $context = [
            'token'     => null,
            'timestamp' => $timestamp,
        ];
        $response_expected = new Response();
        $accessTokenMock = Mockery::mock(AccessTokenService::class);
        $accessTokenMock->shouldReceive('setUser')
            ->once()
            ->with($user);
        $accessTokenMock->shouldNotReceive('generate');
        $accessTokenMock->shouldNotReceive('delete');
        $accessTokenMock->shouldReceive('createdAt')
            ->once()
            ->andReturn($timestamp);
        $responderMock = Mockery::mock(UpdateAccessTokenViewResponder::class);
        $responderMock->shouldReceive('response')
            ->once()
            ->with($context)
            ->andReturn($response_expected);
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('user')
            ->once()
            ->andReturn($user);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('post')
            ->andReturn(false);
        $requestMock->shouldReceive('isMethod')
            ->once()
            ->with('delete')
            ->andReturn(false);

        $controller = new UpdateAccessTokenActionHTML($accessTokenMock, $responderMock);
        $response_actual = $controller->__invoke($requestMock);

        $this->assertSame($response_expected, $response_actual);
    }
}
