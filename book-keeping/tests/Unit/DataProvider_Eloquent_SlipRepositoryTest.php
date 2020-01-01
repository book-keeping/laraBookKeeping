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
        $slipId = $this->slip->create($bookId, $outline, $date, $memo, $isDraft);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_bound_on' => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
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
            'book_bound_on' => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft,
        ])->slip_id;
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->slip->updateIsDraft($slipId, $isDraft_updated);

        $this->assertDatabaseHas('bk2_0_slips', [
            'slip_id'       => $slipId,
            'book_bound_on' => $bookId,
            'slip_outline'  => $outline,
            'slip_memo'     => $memo,
            'date'          => $date,
            'is_draft'      => $isDraft_updated,
        ]);
    }
}
