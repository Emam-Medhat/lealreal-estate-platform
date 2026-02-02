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
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('buyer_id')->nullable()->constrained('users')->after('template_id');
            $table->foreignId('seller_id')->nullable()->constrained('users')->after('buyer_id');
            $table->foreignId('property_id')->nullable()->constrained()->after('seller_id');
            
            $table->index(['buyer_id']);
            $table->index(['seller_id']);
            $table->index(['property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['buyer_id']);
            $table->dropForeign(['seller_id']);
            $table->dropForeign(['property_id']);
            $table->dropIndex(['buyer_id']);
            $table->dropIndex(['seller_id']);
            $table->dropIndex(['property_id']);
            $table->dropColumn(['buyer_id', 'seller_id', 'property_id']);
        });
    }
};
