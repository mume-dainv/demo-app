<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('log_import', function (Blueprint $table) {
            $table->dropColumn('messages');
            $table->json('errors')->nullable();
            $table->integer('total_row');
            $table->integer('row_fail')->default(0);
            $table->integer('row_success')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_import', function (Blueprint $table) {
            $table->addColumn('json', 'messages');
            $table->dropColumn('errors');
            $table->dropColumn('total_row');
            $table->dropColumn('row_fail');
            $table->dropColumn('row_success');
        });
    }
};
