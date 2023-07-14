<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_id')->unique();
            $table->string('job_link')->unique();
            $table->text('type')->nullable();
            $table->string('work_from')->nullable();
            $table->integer('experience')->nullable();
            $table->integer('batch')->nullable();
            $table->string('branch')->nullable();
            $table->double('package', 9, 4)->nullable();
            $table->text('company_name')->nullable();
            $table->text('company_location')->nullable();
            $table->text('about_company')->nullable();
            $table->text('post')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->text('requirement')->nullable();
            $table->text('job_description')->nullable();
            $table->text('instruction')->nullable();
            $table->string('attachment')->nullable();
            //post,draft,expire
            $table->string('status');
            $table->tinyInteger('is_deleted');
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
        Schema::dropIfExists('jobs');
    }
}
