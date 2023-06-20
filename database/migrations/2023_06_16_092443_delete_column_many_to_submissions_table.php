<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteColumnManyToSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['is_approved','is_rejected','feedback_at','feedback_content','is_approved_level2','is_rejected_level2','image_count','send_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_rejected')->default(false);
            $table->dateTime('feedback_at')->nullable();
            $table->text('feedback_content')->nullable();
            $table->boolean('is_approved_level2')->default(false);
            $table->boolean('is_rejected_level2')->default(false);
            $table->integer('image_count')->default(1);
            $table->dateTime('send_date')->nullable();

        });
    }
}
