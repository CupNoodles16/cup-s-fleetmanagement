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
        Schema::create('order_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->enum('type', ['pickup','delivery','waypoint'])->default('delivery');
            $table->string('address_line');
            $table->string('city', 100);
            $table->string('province', 100)->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['pending','en_route','arrived','completed','skipped'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'sequence']);
            $table->index(['order_id', 'sequence']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_stops');
    }
};
