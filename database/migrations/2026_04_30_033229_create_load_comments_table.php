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
        Schema::create('load_comments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('load_id')->constrained('loads')->cascadeOnDelete();
        $table->foreignId('user_id')->constrained('users');
        $table->enum('visibility', ['internal', 'driver'])->default('internal');
        $table->text('message');
        $table->timestamps();

        $table->index(['load_id', 'visibility']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_comments');
    }
};
