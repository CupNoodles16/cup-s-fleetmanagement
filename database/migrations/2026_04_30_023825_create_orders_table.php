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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cargo_description', 255);
            $table->enum('cargo_type', ['general','fragile','perishable','hazardous','bulk','livestock'])->default('general');
            $table->unsignedInteger('weight_kg');
            $table->unsignedInteger('volume_cbm')->nullable();
            $table->enum('required_vehicle_type', ['closed_van','open_truck','flatbed','refrigerated','tanker','trailer','any'])->default('any');
            $table->enum('status', ['draft','confirmed','assigned','in_transit','partially_delivered','delivered','cancelled','failed'])->default('draft');
            $table->enum('priority', ['normal','urgent','critical'])->default('normal');
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('delivery_deadline_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->decimal('quoted_amount', 12, 2)->nullable();
            $table->decimal('final_amount', 12, 2)->nullable();
            $table->enum('payment_status', ['unpaid','invoiced','paid','overdue'])->default('unpaid');
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('tracking_token')->unique()->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority', 'created_at']);
            $table->index(['customer_id', 'status', 'created_at']);
            $table->index(['status', 'delivery_deadline_at']);
            $table->index(['payment_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
