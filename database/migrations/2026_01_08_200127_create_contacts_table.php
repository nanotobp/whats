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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('name')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->foreignId('group_id')->nullable()->constrained()->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
