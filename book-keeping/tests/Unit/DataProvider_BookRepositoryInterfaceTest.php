<?php

namespace Tests\Unit;

use Tests\TestCase;

abstract class DataProvider_BookRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $title = 'string';

        $bookId = $this->book->create($title);

        $this->assertTrue(is_string($bookId));
    }
}
