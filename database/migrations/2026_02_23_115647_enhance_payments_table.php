<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('property_id')->constrained()->onDelete('set null');
            $table->string('idempotency_key')->unique()->nullable()->after('transaction_id');
            $table->string('secure_token')->nullable()->after('idempotency_key');
            $table->ipAddress('ip_address')->nullable()->after('metadata');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('fraud_status')->nullable()->after('user_agent'); // clean, flagged, blocked
            $table->timestamp('expires_at')->nullable()->after('processed_at');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id', 'idempotency_key', 'secure_token', 'ip_address', 'user_agent', 'fraud_status', 'expires_at']);
        });
    }
};
