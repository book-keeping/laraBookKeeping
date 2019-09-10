<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_slips', function (Blueprint $table) {
            $table->uuid('slip_id')->primary();
            $table->uuid('book_bound_on');
            $table->foreign('book_bound_on')->references('book_id')->on('bk2_0_books');
            $table->string('slip_outline', 200);
            $table->string('slip_memo', 500);
            $table->date('date');
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
        Schema::dropIfExists('bk2_0_slips');
    }
}
