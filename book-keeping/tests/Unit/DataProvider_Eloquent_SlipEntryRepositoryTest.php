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
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 10,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => 200,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
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
            'slip_id'        => $slipId,
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
    public function delete_OneRecordIsSoftDeleted()
    {
        $slipId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $client = 'client5';
        $outline = 'outline5';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 1234,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slipEntry->delete($slipEntryId);

        $this->assertSoftDeleted('bk2_0_slip_entries', [
            'slip_entry_id' => $slipEntryId,
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => 1234,
            'client'        => $client,
            'outline'       => $outline,
        ]);
    }

    /**
     * @test
     */
    public function findAllBySlipId_ReturnSlipEntries()
    {
        $slipId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $amount = 2468;
        $client = 'client6';
        $outline = 'outline6';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slipEntries_expected = [
            [
                'slip_entry_id' => $slipEntryId,
                'slip_id'       => $slipId,
                'debit'         => $accountId1,
                'credit'        => $accountId2,
                'amount'        => $amount,
                'client'        => $client,
                'outline'       => $outline,
            ],
        ];

        $slipEntries_actual = $this->slipEntry->findAllBySlipId($slipId);

        $this->assertSame($slipEntries_expected, $slipEntries_actual);
    }

    /**
     * @test
     */
    public function findById_ReturnOneSlipEntry()
    {
        $slipId = (string) Str::uuid();
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $amount = 36912;
        $client = 'client7';
        $outline = 'outline7';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipEntryId = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slipEntry_expected = [
            'slip_entry_id' => $slipEntryId,
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount,
            'client'        => $client,
            'outline'       => $outline,
        ];

        $slipEntry_actual = $this->slipEntry->findById($slipEntryId);

        $this->assertSame($slipEntry_expected, $slipEntry_actual);
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnArrayWithSpecifiedCredit()
    {
        $fromDate = '2019-01-01';
        $toDate = '2019-01-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline51';
        $memo = 'memo51';
        $date = '2019-01-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword1';
        $client2 = 'client512';
        $client3 = 'client513';
        $client4 = 'client514';
        $client5 = 'client515';
        $client6 = 'client516';
        $outline1 = 'outline511';
        $outline2 = 'outline512';
        $outline3 = 'outline513';
        $outline5 = 'outline515';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['credit' => $accountId3];

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);

        $this->assertSame(4, count($slipEntries));
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnArrayWithSpecifiedDebit()
    {
        $fromDate = '2019-02-01';
        $toDate = '2019-02-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline52';
        $memo = 'memo52';
        $date = '2019-02-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword2';
        $client2 = 'client522';
        $client3 = 'client523';
        $client4 = 'client524';
        $client5 = 'client525';
        $client6 = 'client526';
        $outline1 = 'outline521';
        $outline2 = 'outline522';
        $outline3 = 'outline523';
        $outline5 = 'outline525';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['debit' => $accountId1];

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);

        $this->assertSame(2, count($slipEntries));
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnArrayWithSpecifiedDebitAndCredit()
    {
        $fromDate = '2019-03-01';
        $toDate = '2019-03-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline53';
        $memo = 'memo53';
        $date = '2019-03-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword3';
        $client2 = 'client532';
        $client3 = 'client533';
        $client4 = 'client534';
        $client5 = 'client535';
        $client6 = 'client536';
        $outline1 = 'outline531';
        $outline2 = 'outline532';
        $outline3 = 'outline533';
        $outline5 = 'outline535';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['debit' => $accountId1, 'credit' => $accountId3, 'and_or' => 'and'];

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);

        $this->assertSame(1, count($slipEntries));
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnArrayWithSpecifiedDebitOrCredit()
    {
        $fromDate = '2019-04-01';
        $toDate = '2019-04-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline54';
        $memo = 'memo54';
        $date = '2019-04-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword4';
        $client2 = 'client542';
        $client3 = 'client543';
        $client4 = 'client544';
        $client5 = 'client545';
        $client6 = 'client546';
        $outline1 = 'outline541';
        $outline2 = 'outline542';
        $outline3 = 'outline543';
        $outline5 = 'outline545';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['debit' => $accountId1, 'credit' => $accountId3, 'and_or' => 'or'];

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);

        $this->assertSame(5, count($slipEntries));
    }

    /**
     * @test
     */
    public function searchSlipEntries_ReturnArrayWithSpecifiedKeyword()
    {
        $fromDate = '2019-05-01';
        $toDate = '2019-05-31';
        $bookId = (string) Str::uuid();
        $slipOutline = 'slip_outline55';
        $memo = 'memo55';
        $date = '2019-05-10';
        $isDraft = false;
        $accountId1 = (string) Str::uuid();
        $accountId2 = (string) Str::uuid();
        $accountId3 = (string) Str::uuid();
        $accountId4 = (string) Str::uuid();
        $accountId5 = (string) Str::uuid();
        $accountId6 = (string) Str::uuid();
        $accountId7 = (string) Str::uuid();
        $accountId8 = (string) Str::uuid();
        $amount1 = 1000;
        $amount2 = 2000;
        $amount3 = 3000;
        $amount4 = 4000;
        $amount5 = 5000;
        $amount6 = 6000;
        $keyword = 'keyword5';
        $client2 = 'client552';
        $client3 = 'client553';
        $client4 = 'client554';
        $client5 = 'client555';
        $client6 = 'client556';
        $outline1 = 'outline551';
        $outline2 = 'outline552';
        $outline3 = 'outline553';
        $outline5 = 'outline555';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId1 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId2,
            'amount'        => $amount1,
            'client'        => $keyword,
            'outline'       => $outline1,
        ])->slip_entry_id;
        $slipEntryId2 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId1,
            'credit'        => $accountId3,
            'amount'        => $amount2,
            'client'        => $client2,
            'outline'       => $outline2,
        ])->slip_entry_id;
        $slipEntryId3 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId4,
            'credit'        => $accountId3,
            'amount'        => $amount3,
            'client'        => $client3,
            'outline'       => $outline3,
        ])->slip_entry_id;
        $slipEntryId4 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId5,
            'credit'        => $accountId3,
            'amount'        => $amount4,
            'client'        => $client4,
            'outline'       => $keyword,
        ])->slip_entry_id;
        $slipEntryId5 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId6,
            'credit'        => $accountId3,
            'amount'        => $amount5,
            'client'        => $client5,
            'outline'       => $outline5,
        ])->slip_entry_id;
        $slipEntryId6 = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $accountId7,
            'credit'        => $accountId8,
            'amount'        => $amount6,
            'client'        => $client6,
            'outline'       => $keyword,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $condition = ['keyword' => $keyword];

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, $condition, $bookId);

        $this->assertSame(3, count($slipEntries));
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
            'book_id'       => $bookId,
            'slip_outline'  => $slipOutline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        $slipEntryId = factory(SlipEntry::class)->create([
            'slip_id'       => $slipId,
            'debit'         => $debit,
            'credit'        => $credit,
            'amount'        => $amount,
            'client'        => $client,
            'outline'       => $outline,
        ])->slip_entry_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $slipEntries = $this->slipEntry->searchSlipEntries($fromDate, $toDate, [], $bookId);

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
