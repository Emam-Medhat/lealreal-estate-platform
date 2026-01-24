<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lead_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // email, social, ads, events
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->json('target_audience')->nullable(); // criteria for targeting
            $table->json('goals')->nullable(); // campaign objectives
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->index(['type']);
            $table->index(['is_active']);
            $table->index(['start_date', 'end_date']);
        });

        Schema::create('campaign_lead_pivot', function (Blueprint $table) {
            $table->foreignId('campaign_id')->constrained('lead_campaigns')->onDelete('cascade');
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active, converted, lost
            $table->json('metadata')->nullable(); // campaign-specific data
            $table->timestamps();
            
            $table->primary(['campaign_id', 'lead_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_lead_pivot');
        Schema::dropIfExists('lead_campaigns');
    }
};
