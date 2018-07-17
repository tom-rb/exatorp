<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectionProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selection_processes', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('open_date');
            $table->dateTime('close_date');
            $table->text('questions')->nullable();
            $table->timestamps();
        });

        Schema::create('selection_process_positions', function (Blueprint $table) {
            $table->unsignedInteger('selection_process_id');
            $table->morphs('position');

            // The position_id and position_type are created by the morphs column
            $table->primary(['selection_process_id', 'position_id', 'position_type'], 'selection_process_positions_primary');
            $table->foreign('selection_process_id')->references('id')
                ->on('selection_processes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selection_process_positions');
        Schema::dropIfExists('selection_processes');
    }
}
