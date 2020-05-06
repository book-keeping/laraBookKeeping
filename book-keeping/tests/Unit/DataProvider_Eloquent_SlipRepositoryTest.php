<?php

namespace Tests\Unit;

use App\DataProvider\Eloquent\Slip;
use App\DataProvider\Eloquent\SlipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataProvider_Eloquent_SlipRepositoryTest extends DataProvider_SlipRepositoryInterfaceTest
{
    use RefreshDatabase;

    protected $slip;

    public function setUp(): void
    {
        parent::setUp();
        $this->slip = new SlipRepository();
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    /**
     * @test
     */
    public function create_OneRecordIsCreated()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline2';
        $date = '2019-07-02';
        $memo = 'memo2';
        $isDraft = false;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, null, $isDraft);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ]);
    }

    /**
     * @test
     */
    public function delete_OneRecordIsSoftDeleted()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline4';
        $memo = 'memo4';
        $date = '2019-09-03';
        $isDraft = false;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slip->delete($slipId);

        $this->assertSoftDeleted('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ]);
    }

    /**
     * @test
     */
    public function findAllDraftByBookId_ReturnSlips()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline5';
        $memo = 'memo5';
        $date = '2019-10-03';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => true,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slips_expected = [
            ['slip_id' => $slipId, 'date' => $date, 'slip_outline' => $outline, 'slip_memo' => $memo],
        ];

        $slips_actual = $this->slip->findAllDraftByBookId($bookId);

        $this->assertSame($slips_expected, $slips_actual);
    }

    /**
     * @test
     */
    public function findById_ReturnSlip()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline120';
        $memo = 'memo120';
        $date = '2019-01-22';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => false,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $slip_expected = ['book_id' => $bookId, 'slip_id' => $slipId, 'date' => $date, 'slip_outline' => $outline, 'slip_memo' => $memo];

        $slip_actual = $this->slip->findById($slipId);

        $this->assertSame($slip_expected, $slip_actual);
    }

    /**
     * @test
     */
    public function update_OneRecordIsUpdated()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline6';
        $memo = 'memo6';
        $date = '2019-11-03';
        $isDraft = false;
        $outline_updated = 'outline6_updated';
        $memo_updated = 'memo6_updated';
        $date_updated = '2019-12-03';
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slip->update($slipId, [
            'outline'  => $outline_updated,
            'memo'     => $memo_updated,
            'date'     => $date_updated,
        ]);

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_id'       => $bookId,
            'slip_outline'  => $outline_updated,
            'slip_memo'     => $memo_updated,
            'date'          => $date_updated,
            'is_draft'      => $isDraft,
        ]);
    }

    /**
     * @test
     */
    public function updateIsDraft_IsDraftIsUpdated()
    {
        $bookId = (string) Str::uuid();
        $outline = 'outline3';
        $date = '2019-07-03';
        $memo = 'memo3';
        $isDraft = false;
        $isDraft_updated = true;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $slipId = factory(Slip::class)->create([
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slip->updateIsDraft($slipId, $isDraft_updated);

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_id'       => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft_updated,
        ]);
    }
}
