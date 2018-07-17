<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('member_id');
            $table->unsignedInteger('selection_process_id');
            $table->unsignedInteger('first_area_id');
            $table->unsignedInteger('first_area_job')->nullable();
            $table->unsignedInteger('second_area_id')->nullable();
            $table->unsignedInteger('second_area_job')->nullable();
            $table->mediumText('answers')->nullable();
            $table->string('how_did_you_hear')->nullable();
            $table->boolean('trying_first_option')->default(true);
            $table->integer('status')->nullable();
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('selection_process_id')->references('id')->on('selection_processes')->onDelete('cascade');
        });

        // Options for the "How did you hear about us?" question.
        Schema::create('how_did_you_hear_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->timestamps();
            $table->softDeletes();
        });

        // Candidates on hold mailing list
        Schema::create('candidates_on_hold', function (Blueprint $table) {
            $table->unsignedInteger('member_id');
            $table->timestamps();

            $table->primary('member_id');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('candidates_on_hold');
        Schema::dropIfExists('how_did_you_hear_options');
        Schema::dropIfExists('member_applications');
    }
}
