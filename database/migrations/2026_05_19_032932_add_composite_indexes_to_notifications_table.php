<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add composite index for (notifiable_type, notifiable_id, created_at)
            $table->index(['notifiable_type', 'notifiable_id', 'created_at'], 'notifications_type_id_created_idx');

            // Add composite index for (notifiable_type, notifiable_id, read_at)
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_type_id_read_idx');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_type_id_created_idx');
            $table->dropIndex('notifications_type_id_read_idx');
        });
    }
};
