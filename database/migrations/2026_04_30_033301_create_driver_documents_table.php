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
        Schema::create('driver_documents', function (Blueprint $table) {
        $table->id();
        $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
        $table->enum('type', [
            'profile_photo',
            'license_front',
            'license_back',
            'nbi_clearance',
            'medical_certificate',
            'drug_test_result'
        ]);
        $table->string('disk', 20)->default('local');
        $table->string('path');
        $table->string('original_filename')->nullable();
        $table->string('mime_type', 50)->nullable();
        $table->unsignedInteger('size_bytes')->nullable();
        $table->date('expires_at')->nullable();
        $table->boolean('is_verified')->default(false);
        $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('verified_at')->nullable();
        $table->foreignId('uploaded_by')->constrained('users');
        $table->timestamp('created_at')->useCurrent();

        $table->index(['driver_id', 'type']);
        $table->index(['driver_id', 'expires_at']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_documents');
    }
};
