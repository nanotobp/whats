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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
            $table->index(['group_id', 'status']);
            $table->index(['group_id', 'created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index('status');
            $table->index(['campaign_id', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index('read_at');
            $table->index('sent_at');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->index('is_valid');
            $table->index(['group_id', 'is_valid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['group_id', 'status']);
            $table->dropIndex(['group_id', 'created_at']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['campaign_id', 'status']);
            $table->dropIndex(['contact_id', 'status']);
            $table->dropIndex(['read_at']);
            $table->dropIndex(['sent_at']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['is_valid']);
            $table->dropIndex(['group_id', 'is_valid']);
        });
    }
};
