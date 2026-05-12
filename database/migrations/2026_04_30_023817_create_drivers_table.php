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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('current_vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->string('license_number', 50)->unique();
            $table->enum('license_type', ['professional', 'non_professional']);
            $table->date('license_expiry');
            $table->enum('status', ['available','on_trip','off_duty','on_leave','suspended'])->default('available');
            $table->unsignedSmallInteger('hos_remaining_minutes')->default(600);
            $table->decimal('last_lat', 10, 7)->nullable();
            $table->decimal('last_lng', 10, 7)->nullable();
            $table->timestamp('last_location_at')->nullable();
            $table->decimal('performance_rating', 3, 2)->default(5.00);
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->string('phone', 20)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'hos_remaining_minutes']);
            $table->index(['last_lat', 'last_lng']);
            $table->index('license_expiry');
            $table->index('performance_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
