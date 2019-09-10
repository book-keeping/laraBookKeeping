<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bk2_0_permissions', function (Blueprint $table) {
            $table->uuid('permission_id')->primary();
            $table->unsignedBigInteger('permitted_user');
            $table->foreign('permitted_user')->references('id')->on('users');
            $table->uuid('readable_book');
            $table->foreign('readable_book')->references('book_id')->on('bk2_0_books');
            $table->boolean('modifiable');
            $table->boolean('is_owner');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bk2_0_permissions');
    }
}
