<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('crm_leads')) {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('source', ['website', 'phone', 'referral', 'social_media', 'walk_in', 'other']);
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'lost'])->default('new');
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('property_type')->nullable();
            $table->text('notes')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('crm_leads');
    }
};
