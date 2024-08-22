<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->integer('project_id');
            $table->text('notes')->nullable();
            $table->string('author');
            $table->string('document_date')->nullable();
            $table->string('document_type');
            $table->boolean('published')->default(FALSE);
            $table->dateTime('publish_date', 0);
            $table->boolean('checked_out')->default(FALSE);
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
        Schema::dropIfExists('documents');
    }
}
