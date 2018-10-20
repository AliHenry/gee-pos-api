<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutletSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_setting', function (Blueprint $table) {
            $table->uuid('outlet_uuid');
            $table->string('currency')->nullable();
            $table->string('email')->nullable();
            $table->string('facebook')->nullable();
            $table->boolean('online')->default(true);
            $table->text('tags')->nullable();
            $table->text('open_hours')->nullable();
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
        Schema::dropIfExists('outlet_setting');
    }
}
