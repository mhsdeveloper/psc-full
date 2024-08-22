<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDocumentsProjectId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('documents', function (Blueprint $table) {
			$table->string('project_sitename')->default("");
        });
 
		Schema::table('projects', function (Blueprint $table) {
			$table->string('project_sitename')->default("");
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
			$table->dropColumn('project_sitename');
        });
        Schema::table('projects', function (Blueprint $table) {
			$table->dropColumn('project_sitename');
        });
    }
}
