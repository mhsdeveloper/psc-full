<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NewDocFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
			$table->string('authors')->default("");
			$table->string('recipients')->default("");
			$table->string('title')->default("");
			$table->string('teaser')->default("");
			$table->string('persrefs')->default("");
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
			$table->dropColumn('authors');
			$table->dropColumn('recipients');
			$table->dropColumn('title');
			$table->dropColumn('teaser');
			$table->dropColumn('persrefs');
        });

	}
}
