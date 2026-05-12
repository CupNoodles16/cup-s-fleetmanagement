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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number', 20)->unique();
            $table->string('model', 100);
            $table->smallInteger('year')->nullable();
            $table->enum('type', ['closed_van','open_truck','flatbed','refrigerated','tanker','trailer']);
            $table->unsignedInteger('capacity_kg');
            $table->enum('status', ['available','on_trip','in_maintenance','retired'])->default('available');
            $table->date('registration_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->unsignedInteger('odometer_km')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('status');
            $table->index('registration_expiry');
            $table->index('insurance_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
