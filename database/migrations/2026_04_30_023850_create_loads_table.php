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
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->string('load_number', 20)->unique();
            $table->foreignId('order_id')->constrained('orders')->restrictOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['unassigned','assigned','driver_accepted','en_route_pickup','at_pickup','loaded','en_route_delivery','at_delivery','delivered','failed','cancelled'])->default('unassigned');
            $table->enum('assignment_type', ['manual','auto'])->default('manual');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('driver_accepted_at')->nullable();
            $table->timestamp('pickup_arrived_at')->nullable();
            $table->timestamp('pickup_departed_at')->nullable();
            $table->timestamp('delivery_arrived_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('eta_at')->nullable();
            $table->boolean('is_delayed')->default(false);
            $table->unsignedSmallInteger('delay_minutes')->nullable();
            $table->string('bol_path')->nullable();
            $table->string('pod_signature_path')->nullable();
            $table->timestamp('pod_captured_at')->nullable();
            $table->foreignId('pod_captured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('driver_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_delayed']);
            $table->index(['driver_id', 'status']);
            $table->index(['vehicle_id', 'status']);
            $table->index(['status', 'eta_at']);
            $table->index(['order_id', 'status']);
            $table->index(['is_delayed', 'delivered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loads');
    }
};
