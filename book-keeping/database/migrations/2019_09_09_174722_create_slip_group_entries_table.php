<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlipGroupEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_slip_group_entries', function (Blueprint $table) {
            $table->uuid('slip_group_entry_id')->primary();
            $table->uuid('slip_group_id');
            $table->foreign('slip_group_id')->references('slip_group_id')->on('bk2_0_slip_groups');
            $table->uuid('related_slip');
            $table->foreign('related_slip')->references('slip_id')->on('bk2_0_slips');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bk2_0_slip_group_entries');
    }
}
