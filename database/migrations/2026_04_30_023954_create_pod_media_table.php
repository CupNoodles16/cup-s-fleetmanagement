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
        Schema::create('pod_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('load_id')->constrained('loads')->cascadeOnDelete();
            $table->foreignId('order_stop_id')->nullable()->constrained('order_stops')->nullOnDelete();
            $table->enum('type', ['signature','photo','document']);
            $table->string('disk', 20)->default('local');
            $table->string('path');
            $table->string('original_filename')->nullable();
            $table->unsignedInteger('size_bytes')->nullable();
            $table->string('mime_type', 50)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['load_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pod_media');
    }
};
