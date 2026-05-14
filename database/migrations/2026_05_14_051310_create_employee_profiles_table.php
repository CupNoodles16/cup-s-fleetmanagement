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
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Personal Information
            $table->string('surname');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix')->nullable(); // Jr, Sr, II, III, IV
            $table->date('birth_date');
            $table->enum('sex', ['male', 'female', 'other']);
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed']);

            // Contact Information
            $table->string('phone_number'); // +63XXXXXXXXXX format
            $table->text('address');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_number'); // +63XXXXXXXXXX format
            $table->string('emergency_contact_relationship'); // Mother, Father, Spouse, Sibling, etc.

            // Document paths (stored locally in storage/app/public/documents/)
            $table->string('health_card_path')->nullable();
            $table->string('nbi_clearance_path')->nullable();
            $table->string('police_clearance_path')->nullable();

            $table->timestamps();

            // Indexes for faster queries
            $table->index(['surname', 'first_name']);
            $table->index('phone_number');
            $table->index('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_profiles');
    }
};
