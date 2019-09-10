<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlipEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_slip_entries', function (Blueprint $table) {
            $table->uuid('slip_entry_id')->primary();
            $table->uuid('slip_bound_on');
            $table->foreign('slip_bound_on')->references('slip_id')->on('bk2_0_slips');
            $table->uuid('debit');
            $table->foreign('debit')->references('account_id')->on('bk2_0_accounts');
            $table->uuid('credit');
            $table->foreign('credit')->references('account_id')->on('bk2_0_accounts');
            $table->bigInteger('amount');
            $table->string('client', 40);
            $table->string('outline', 200);
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
        Schema::dropIfExists('bk2_0_slip_entries');
    }
}
