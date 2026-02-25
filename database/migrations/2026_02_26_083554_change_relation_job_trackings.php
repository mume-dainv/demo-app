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
        Schema::table('job_trackings', function (Blueprint $table) {
            $table->dropForeign('job_trackings_log_import_id_foreign');
            $table->dropUnique('job_trackings_log_import_id_unique');
            $table->dropColumn('log_import_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_trackings', function (Blueprint $table) {
            $table->foreignId('job_tracking_id')->unique()->constrained('job_trackings')->cascadeOnDelete();
        });
    }
};
