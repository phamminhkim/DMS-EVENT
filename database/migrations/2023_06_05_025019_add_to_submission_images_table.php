<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToSubmissionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submission_images', function (Blueprint $table) {
            $table->boolean('is_approved_level2')->default(false);
            $table->boolean('is_rejected_level2')->default(false);
            $table->string('feedback_content')->nullable();
            $table->dateTime('send_date')->nullable();
            $table->foreignId('program_stage_id')->constrained('program_stages');
            $table->bigInteger('image_id')->unsigned();
            $table->integer('image_count')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submission_images', function (Blueprint $table) {
            $table->removeColumn('is_approved_level2');
            $table->removeColumn('is_rejected_level2');
            $table->removeColumn('feedback_content');
            $table->removeColumn('send_date');
            $table->removeColumn('program_stage_id');
            $table->removeColumn('image_count');
            $table->removeColumn('image_id');
        });
    }
}
