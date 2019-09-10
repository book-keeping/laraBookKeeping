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
            $table->uuid('book_bound_on');
            $table->foreign('book_bound_on')->references('book_id')->on('bk2_0_books');
            $table->uuid('account_code');
            $table->foreign('account_code')->references('account_id')->on('bk2_0_accounts');
            $table->date('date');
            $table->bigInteger('amount');
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
