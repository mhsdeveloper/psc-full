<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NamesTitleVariantsProfessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('names', function (Blueprint $table) {
            $table->string('title')->default("");
            $table->string('variants')->default("");
            $table->text('professions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('names', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('variants');
            $table->dropColumn('professions');
        });
    }
}
