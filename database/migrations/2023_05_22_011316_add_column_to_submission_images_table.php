<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToSubmissionImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submission_images', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_rejected')->default(false);
            $table->dateTime('feedback_at')->nullable();
            $table->foreignId('feedback_by')->nullable()->constrained('users');
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
            $table->dropColumn('is_approved');
            $table->dropColumn('is_rejected');
            $table->dropColumn('feedback_at');
            $table->dropColumn('feedback_by');
        });
    }
}
