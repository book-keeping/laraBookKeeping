<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

abstract class DataProvider_SlipEntryRepositoryInterfaceTest extends TestCase
{
    /**
     * @test
     */
    public function calculateSum_ReturnValueTypeIsArray()
    {
        $fromDate = '2019-08-01';
        $toDate = '2019-08-31';
        $bookId = (string) Str::uuid();

        $sumList = $this->slipEntry->calculateSum($fromDate, $toDate, $bookId);

        $this->assertTrue(is_array($sumList));
    }

    /**
     * @test
     */
    public function create_ReturnValueTypeIsString()
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 10000;
        $client = 'client1';
        $outline = 'outline1';
        $displayOrder = 1;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = $this->slipEntry->create($slipId, $debit, $credit, $amount, $client, $outline, $displayOrder);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(is_string($slipEntryId));
    }

    /**
     * @test
     */
    public function delete_CalledWithString()
    {
        $slipEntryId = (string) Str::uuid();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->slipEntry->delete($slipEntryId);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function findAllBySlipId_ReturnValueTypeIsArray()
    {
        $slipId = (string) Str::uuid();

        $slipEntries = $this->slipEntry->findAllBySlipId($slipId);

        $this->assertIsArray($slipEntries);
    }

    /**
     * @test
     */
    public function findById_ReturnValueTypeIsArrayOrNull()
    {
        $slipEntryId = (string) Str::uuid();

        $slipEntries = $this->slipEntry->findById($slipEntryId);

        if (is_null($slipEntries)) {
            $this->assertNull($slipEntries);
        } else {
            $this->assertIsArray($slipEntries);
        }
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnValueTypeIsArray()
    {
        $fromDate = '2019-09-15';
        $toDate = '2019-09-30';
        $bookId = (string) Str::uuid();

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, [], $bookId);

        $this->assertTrue(is_array($slipEntries));
    }
}
