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
        Schema::table('log_import', function (Blueprint $table) {
            $table->foreignId('job_tracking_id')->unique()->constrained('job_trackings')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_import', function (Blueprint $table) {
            $table->dropForeign('log_import_job_tracking_id_foreign');
            $table->dropUnique('log_import_job_tracking_id_unique');
            $table->dropColumn('job_tracking_id');
        });
    }
};
