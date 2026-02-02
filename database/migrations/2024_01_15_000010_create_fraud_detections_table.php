<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fraud_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('detection_type'); // listing_fraud, payment_fraud, identity_fraud
            $table->decimal('risk_score', 5, 2); // 0-100
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->text('description');
            $table->json('indicators')->nullable(); // suspicious patterns detected
            $table->json('evidence')->nullable(); // supporting data
            $table->enum('status', ['pending', 'investigating', 'confirmed', 'false_positive', 'resolved'])->default('pending');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fraud_detections');
    }
};
