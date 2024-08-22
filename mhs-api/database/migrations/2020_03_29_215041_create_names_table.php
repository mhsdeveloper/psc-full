<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('names', function (Blueprint $table) {
            $table->id();
            // $table->string('name_key');
            $table->string('family_name');
            $table->string('given_name');
            $table->string('middle_name')->nullable();
            $table->string('maiden_name')->nullable();
            $table->string('suffix')->nullable();
            $table->string('keywords')->nullable();
            $table->string('date_of_birth')->nullable();
            $table->string('date_of_death')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->string('bio_filename')->nullable();
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
        Schema::dropIfExists('names');
    }
}
