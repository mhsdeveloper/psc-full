<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocumentRemoveCols extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('documents', function (Blueprint $table) {
			$table->dropColumn('document_date');
			$table->dropColumn('document_type');
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
			$table->string('document_date');
			$table->string('document_type');
        });
    }
}
