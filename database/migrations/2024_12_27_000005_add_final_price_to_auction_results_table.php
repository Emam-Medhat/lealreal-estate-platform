<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_results', function (Blueprint $table) {
            if (!Schema::hasColumn('auction_results', 'final_price')) {
                $table->decimal('final_price', 15, 2)->nullable()->after('winning_bid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auction_results', function (Blueprint $table) {
            if (Schema::hasColumn('auction_results', 'final_price')) {
                $table->dropColumn('final_price');
            }
        });
    }
};
