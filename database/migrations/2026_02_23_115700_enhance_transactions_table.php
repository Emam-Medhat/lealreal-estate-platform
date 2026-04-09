<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            $table->string('gateway_reference')->nullable()->after('status');
            $table->json('payload')->nullable()->after('gateway_reference'); // Full log of the activity
            $table->string('error_code')->nullable()->after('payload');
            $table->text('error_message')->nullable()->after('error_code');
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn(['payment_id', 'gateway_reference', 'payload', 'error_code', 'error_message']);
        });
    }
};
