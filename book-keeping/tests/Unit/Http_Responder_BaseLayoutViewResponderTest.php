<?php

namespace Tests\Unit;

use App\Http\Responder\BaseLayoutViewResponder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class Http_Responder_BaseLayoutViewResponderTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function dropdownMenuLinks_ReturnArray()
    {
        /** @var \Illuminate\Http\Response|\Mockery\MockInterface $ResponseMock */
        $ResponseMock = Mockery::mock(Response::class);
        /** @var \Illuminate\Contracts\View\Factory|\Mockery\MockInterface $ViewFactoryMock */
        $ViewFactoryMock = Mockery::mock(Factory::class);

        $responder = new BaseLayoutViewResponder($ResponseMock, $ViewFactoryMock);
        $dropdownmenuLinks = $responder->dropdownMenuLinks();

        $this->assertIsArray($dropdownmenuLinks);
    }
}
