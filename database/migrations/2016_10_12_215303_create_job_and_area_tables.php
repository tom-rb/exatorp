<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobAndAreaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Areas or departments of the organization
        Schema::create('areas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug');
        });

        // Jobs at each area
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('area_id');
            $table->unsignedInteger('role_id');
            $table->string('description')->nullable();

            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        // Job and Member many-to-many relationship
        Schema::create('job_member', function (Blueprint $table) {
            $table->unsignedInteger('job_id');
            $table->unsignedInteger('member_id');
            $table->timestamps();

            $table->primary(['job_id', 'member_id']);
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
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
        Schema::dropIfExists('job_member');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('areas');
    }
}
