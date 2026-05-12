<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('current_load_id')->nullable()->constrained('loads')->nullOnDelete();
            $table->string('fcm_token')->nullable();
            $table->index('current_load_id');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Drop the foreign key constraint FIRST
            $table->dropForeign(['current_load_id']);

            // Then drop the column
            $table->dropColumn('current_load_id');
            $table->dropColumn('fcm_token');
        });
    }
};
