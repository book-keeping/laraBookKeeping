<?php

namespace Tests\Unit;

use App\DataProvider\SlipEntryRepositoryInterface;
use App\DataProvider\SlipRepositoryInterface;
use App\Service\SlipService;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class Service_SlipServiceTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * @test
     */
    public function createSlipAsDraft_CreateSlipWithSlipEntries()
    {
        $bookId = (string) Str::uuid();
        $outline = 'slip_outline1';
        $date = '2019-08-17';
        $memo = 'memo1';
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $entries = [
            ['debit' => $accountId1, 'credit' => $accountId2, 'amount' => 1100, 'client' => 'client11', 'outline' => 'outline11'],
            ['debit' => $accountId3, 'credit' => $accountId4, 'amount' => 2200, 'client' => 'client22', 'outline' => 'outline22'],
        ];
        $slipId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('create')
            ->once()
            ->with($bookId, $outline, $date, $memo, true)
            ->andReturn($slipId_expected);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId_expected, $entries[0]['debit'], $entries[0]['credit'], $entries[0]['amount'], $entries[0]['client'], $entries[0]['outline']);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId_expected, $entries[1]['debit'], $entries[1]['credit'], $entries[1]['amount'], $entries[1]['client'], $entries[1]['outline']);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipId_actual = $slip->createSlipAsDraft($bookId, $outline, $date, $entries, $memo);

        $this->assertSame($slipId_expected, $slipId_actual);
    }

    /**
     * @test
     */
    public function createSlipEntry_CallRepositoryWithArgumentsAsItIs()
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 6400;
        $client = 'client4';
        $outline = 'outline4';
        $slipEntryId_expected = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('create')
            ->once()
            ->with($slipId, $debit, $credit, $amount, $client, $outline)
            ->andReturn($slipEntryId_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntryId_actual = $slip->createSlipEntry($slipId, $debit, $credit, $amount, $client, $outline);

        $this->assertSame($slipEntryId_expected, $slipEntryId_actual);
    }

    /**
     * @test
     */
    public function deleteSlip_CallRepositoryWithArgumentsAsItIs()
    {
        $slipId = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('delete')
            ->once()
            ->with($slipId);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->deleteSlip($slipId);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function deleteSlipEntry_CallRepositoryWithArgumentsAsItIs()
    {
        $slipEntryId = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('delete')
            ->once()
            ->with($slipEntryId);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->deleteSlipEntry($slipEntryId);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function retrieveAmountFlows_CallRepositoryWithArgumentsAsItIs()
    {
        $fromDate = '2019-09-10';
        $toDate = '2019-09-11';
        $bookId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $amountFlows_expected = [
            $accountId1 => ['debit' => 210, 'credit' => 0],
            $accountId2 => ['debit' => 3000, 'credit' => 10],
            $accountId3 => ['debit' => 0, 'credit' => 3200],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('calculateSum')
            ->once()
            ->with($fromDate, $toDate, $bookId)
            ->andReturn($amountFlows_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $amountFlows_actual = $slip->retrieveAmountFlows($fromDate, $toDate, $bookId);

        $this->assertSame($amountFlows_expected, $amountFlows_actual);
    }

    /**
     * @test
     */
    public function retrieveDraftSlips_CallRepositoryWithArgumentsAsItIs()
    {
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slips_expected = [
            ['slip_id' => $slipId, 'date' => '2019-10-03', 'slip_outline' => 'outline', 'slip_memo' => 'memo'],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('findAllDraftByBookId')
            ->once()
            ->with($bookId)
            ->andReturn($slips_expected);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slips_actual = $slip->retrieveDraftSlips($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }

    /**
     * @test
     */
    public function retrieveSlipEntries_CallRepositoryWithArgumentsAsItIs()
    {
        $fromDate = '2019-08-10';
        $toDate = '2019-08-15';
        $bookId = (string) Str::uuid();
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();

        $slipEntries_expected = [
            [
                'slip_id'       => $slipId,
                'date'          => '2019-08-11',
                'slip_outline'  => 'slip_outline3',
                'slip_memo'     => 'memo3',
                'slip_entry_id' => $slipEntryId,
                'debit'         => $accountId1,
                'credit'        => $accountId2,
                'amount'        => 100,
                'client'        => 'client3',
                'outline'       => 'outline3',
            ],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('searchSlipEntries')
            ->once()
            ->with($fromDate, $toDate, [], $bookId)
            ->andReturn($slipEntries_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntries_actual = $slip->retrieveSlipEntries($fromDate, $toDate, [], $bookId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }

    /**
     * @test
     */
    public function retrieveSlipEntriesBoundTo_CallRepositoryWithArgumentsAsItIs()
    {
        $slipId = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $slipEntries_expected = [
            [
                'slip_entry_id' => $slipEntryId,
                'slip_id'       => $slipId,
                'debit'         => $accountId1,
                'credit'        => $accountId2,
                'amount'        => 2240,
                'client'        => 'client5',
                'outline'       => 'outline5',
            ],
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('findAllBySlipId')
            ->once()
            ->with($slipId)
            ->andReturn($slipEntries_expected);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipEntries_actual = $slip->retrieveSlipEntriesBoundTo($slipId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }

    /**
     * @test
     */
    public function retrieveSlipThatBound_CallRepositoryWithArgumentsAsItIs()
    {
        $slipId_expected = (string) Str::uuid();
        $slipEntryId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $slipEntry = [
            'slip_entry_id' => $slipEntryId,
            'slip_id'       => $slipId_expected,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 2560,
            'client'        => 'client6',
            'outline'       => 'outline6',
        ];
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('findById')
            ->once()
            ->with($slipEntryId)
            ->andReturn($slipEntry);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipId_actual = $slip->retrieveSlipThatBound($slipEntryId);

        $this->assertSame($slipId_expected, $slipId_actual);
    }

    /**
     * @test
     */
    public function retrieveSlipThatBound_CallRepositoryWithArgumentsAsItIsAndReturnNull()
    {
        $slipEntryId = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);
        $slipEntryMock->shouldReceive('findById')
            ->once()
            ->with($slipEntryId)
            ->andReturn(null);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slipId = $slip->retrieveSlipThatBound($slipEntryId);

        $this->assertNull($slipId);
    }

    /**
     * @test
     */
    public function submitSlip_CallRepositoryWithArgumentsAsItIs()
    {
        $slipId = (string) Str::uuid();
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('updateIsDraft')
            ->once()
            ->with($slipId, false);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->submitSlip($slipId);

        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function updateDate_CallRepositoryWithArgumentsAsItIs()
    {
        $slipId = (string) Str::uuid();
        $date = '2019-11-01';
        /** @var \App\DataProvider\SlipRepositoryInterface|\Mockery\MockInterface $slipMock */
        $slipMock = Mockery::mock(SlipRepositoryInterface::class);
        $slipMock->shouldReceive('update')
            ->once()
            ->with($slipId, ['date' => $date]);
        /** @var \App\DataProvider\SlipEntryRepositoryInterface|\Mockery\MockInterface $slipEntryMock */
        $slipEntryMock = Mockery::mock(SlipEntryRepositoryInterface::class);

        $slip = new SlipService($slipMock, $slipEntryMock);
        $slip->updateDate($slipId, $date);

        $this->assertTrue(true);
    }
}
