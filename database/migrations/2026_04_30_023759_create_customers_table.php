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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('billing_address');
            $table->string('billing_city', 100);
            $table->string('billing_province', 100)->nullable();
            $table->string('billing_zip', 10)->nullable();
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_name');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
