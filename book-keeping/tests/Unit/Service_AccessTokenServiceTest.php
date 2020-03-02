<?php

namespace Tests\Unit;

use App\Service\AccessTokenService;
use App\User;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class Service_AccessTokenServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createdAt_ReturnTimestamp()
    {
        $user = new User();
        $timestame_expected = new Carbon();
        $user->api_token_created_at = $timestame_expected;

        $accessToken = new AccessTokenService();
        $accessToken->setUser($user);
        $timestamp_actual = $accessToken->createdAt();

        $this->assertSame($timestame_expected, $timestamp_actual);
    }

    /**
     * @test
     */
    public function createdAt_ReturnNullWithoutSettingUser()
    {
        $accessToken = new AccessTokenService();
        $timestamp = $accessToken->createdAt();

        $this->assertNull($timestamp);
    }

    /**
     * @test
     */
    public function delete_DeleteTheToken()
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('forceFill')
            ->once()
            ->with([
                'api_token'            => null,
                'api_token_created_at' => null,
            ])
            ->andReturn($userMock);
        $userMock->shouldReceive('save')
            ->once();

        $accessToken = new AccessTokenService();
        $accessToken->setUser($userMock);
        $accessToken->delete();

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function delete_DoNothingWithoutSettingUser()
    {
        $accessToken = new AccessTokenService();
        $accessToken->delete();

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function generate_SaveAndReturnTheGeneratedToken()
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('forceFill')
            ->once()
            ->andReturn($userMock);
        $userMock->shouldReceive('save')
            ->once();

        $accessToken = new AccessTokenService();
        $accessToken->setUser($userMock);
        $token = $accessToken->generate();

        $this->assertIsString($token);
    }

    /**
     * @test
     */
    public function generate_ReturnNullWithoutSettingUser()
    {
        $accessToken = new AccessTokenService();
        $token = $accessToken->generate();

        $this->assertNull($token);
    }
}
