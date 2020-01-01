<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\Slip;
use App\DataProvider\Eloquent\SlipEntry;
use App\DataProvider\Eloquent\SlipEntryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_SlipEntryRepositoryTest extends DataProvider_SlipEntryRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $slipEntry;

    public function setUp(): void
    {
        parent::setUp();
        $this->slipEntry = new SlipEntryRepository();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function calculateSum_ReturnPairsOfDebitAndCredit()
    {
        $fromDate = '2019-08-01';
        $toDate = '2019-08-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline10';
        $memo = 'memo10';
        $date = '2019-08-02';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $client = 'client10';
        $outline = 'outline10';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_bound_on' => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_bound_on' => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 10,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_bound_on' => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => 200,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_bound_on' => $slipId,
            'debit'         => $accountId2,
            'credit'        => $accountId3,
            'amount'        => 3000,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sumList = $this->slipEntry->calculateSum($fromDate, $toDate, $bookId);

        $this->assertTrue(count($sumList) === 3);
        if (count($sumList) === 3) {
            $this->assertSame(210, $sumList[$accountId1]['debit']);
            $this->assertSame(0, $sumList[$accountId1]['credit']);
            $this->assertSame(3000, $sumList[$accountId2]['debit']);
            $this->assertSame(10, $sumList[$accountId2]['credit']);
            $this->assertSame(0, $sumList[$accountId3]['debit']);
            $this->assertSame(3200, $sumList[$accountId3]['credit']);
        }
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
    {
        $slipId = (string) Str::uuid();
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 10000;
        $client = 'client1';
        $outline = 'outline1';

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = $this->slipEntry->create($slipId, $debit, $credit, $amount, $client, $outline);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slip_entries', [
            'slip_entry_id'  => $slipEntryId,
            'slip_bound_on'  => $slipId,
            'debit'          => $debit,
            'credit'         => $credit,
            'amount'         => $amount,
            'client'         => $client,
            'outline'        => $outline,
        ]);
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnedArrayHasKeysAsSlipEntry()
    {
        $fromDate = '2019-09-15';
        $toDate = '2019-09-30';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline4';
        $memo = 'memo4';
        $date = '2019-09-16';
        $isDraft = false;
        $debit = (string) Str::uuid();
        $credit = (string) Str::uuid();
        $amount = 4000;
        $client = 'client4';
        $outline = 'outline4';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_bound_on' => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId = factory(SlipEntry::class)->create([
            'slip_bound_on' => $slipId,
            'debit'         => $debit,
            'credit'        => $credit,
            'amount'        => $amount,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, $bookId);

        $this->assertFalse(count($slipEntries) === 0);
        if (!(count($slipEntries) === 0)) {
            $this->assertSame([
                'slip_id',
                'date',
                'slip_outline',
                'slip_memo',
                'slip_entry_id',
                'debit',
                'credit',
                'amount',
                'client',
                'outline',
            ], array_keys($slipEntries[0]));
        }
    }
}
