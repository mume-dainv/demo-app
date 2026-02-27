<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('log_exports', function (Blueprint $table) {
            $table->string('status')->nullable(false);
            $table->dropForeign('log_exports_job_tracking_id_foreign');
            $table->dropColumn('job_tracking_id');
        });
        Schema::table('log_import', function (Blueprint $table) {
            $table->dropColumn('row_fail');
            $table->dropColumn('row_success');
            $table->string('status')->nullable(false);
            $table->integer('success_count')->nullable();
            $table->integer('fail_count')->nullable();
            $table->dropForeign('log_import_job_tracking_id_foreign');
            $table->dropColumn('job_tracking_id');
        });
        Schema::dropIfExists('job_trackings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('job_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('job_name')->nullable(false)->unique();
            $table->string('status')->nullable(false);
            $table->foreignId('log_import_id')->unique()->constrained('log_import')->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::table('log_import', function (Blueprint $table) {
            $table->dropColumn('success_count');
            $table->dropColumn('fail_count');
            $table->dropColumn('status');
            $table->integer('row_success')->nullable();
            $table->integer('row_fail')->nullable();
            $table->dropColumn('status');
            $table->foreignId('job_tracking_id')->constrained('job_trackings')->cascadeOnDelete();
        });
        Schema::table('log_exports', function (Blueprint $table) {

            $table->dropColumn('status');
            $table->foreignId('job_tracking_id')->constrained('job_trackings')->cascadeOnDelete();
        });
    }
};
