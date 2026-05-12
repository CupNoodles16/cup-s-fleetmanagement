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
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('load_id')->nullable()->constrained('loads')->nullOnDelete();
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->unsignedSmallInteger('speed_kmh')->nullable();
            $table->unsignedSmallInteger('heading_degrees')->nullable();
            $table->unsignedSmallInteger('accuracy_meters')->nullable();
            $table->timestamp('recorded_at')->useCurrent();

            $table->index(['driver_id', 'load_id', 'recorded_at']);
            $table->index(['load_id', 'recorded_at']);
            $table->index('recorded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_locations');
    }
};
