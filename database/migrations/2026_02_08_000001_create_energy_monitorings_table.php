<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('energy_monitorings')) {
            Schema::create('energy_monitorings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('smart_properties')->onDelete('cascade');
                $table->foreignId('device_id')->nullable()->constrained('iot_devices')->onDelete('cascade');
                $table->decimal('consumption_kwh', 10, 2)->default(0);
                $table->decimal('savings_amount', 10, 2)->default(0);
                $table->decimal('efficiency_score', 5, 2)->default(0);
                $table->string('status')->default('active');
                $table->string('monitoring_type')->default('electricity');
                $table->timestamp('last_reading_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['property_id', 'status']);
                $table->index(['device_id', 'status']);
                $table->index('status');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('energy_monitorings');
    }
};
