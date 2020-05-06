<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_budgets', function (Blueprint $table) {
            $table->uuid('budget_id')->primary();
            $table->uuid('book_id');
            $table->foreign('book_id')->references('book_id')->on('bk2_0_books');
            $table->uuid('account_code');
            $table->foreign('account_code')->references('account_id')->on('bk2_0_accounts');
            $table->date('date');
            $table->bigInteger('amount');
            $table->bigInteger('display_order')->nullable();
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
        Schema::dropIfExists('bk2_0_budgets');
    }
}
