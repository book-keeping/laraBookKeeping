<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_accounts', function (Blueprint $table) {
            $table->uuid('account_id')->primary();
            $table->uuid('account_group_bound_on');
            $table->foreign('account_group_bound_on')->references('account_group_id')->on('bk2_0_account_groups');
            $table->string('title', 40);
            $table->string('description', 200);
            $table->boolean('selectable');
            $table->unsignedBigInteger('bk_uid');
            $table->unsignedBigInteger('bk_code');
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
        Schema::dropIfExists('bk2_0_accounts');
    }
}
