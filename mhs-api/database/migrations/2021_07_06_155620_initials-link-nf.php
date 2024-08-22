<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InitialsLinkNf extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('importrow', function (Blueprint $table) {
            $table->id();
            $table->string('marker');
        });

		Schema::table('names', function (Blueprint $table) {
			$table->string('identifier')->default("");
			$table->string('first_mention')->default("");
        });

		Schema::table('links', function (Blueprint $table) {
			$table->boolean('not_found')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('importrow');

		Schema::table('names', function (Blueprint $table) {
			$table->dropColumn('identifier');
			$table->dropColumn('first_mention');
		});

		Schema::table('links', function (Blueprint $table) {
			$table->dropColumn('not_found');
        });

	}
}
