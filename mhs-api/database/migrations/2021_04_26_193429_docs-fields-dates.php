<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocsFieldsDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
			$table->string('date_from')->default("");
			$table->string('date_to')->default("");
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('documents', function (Blueprint $table) {
			$table->dropColumn('date_from');
			$table->dropColumn('date_to');
        });
    }
}
