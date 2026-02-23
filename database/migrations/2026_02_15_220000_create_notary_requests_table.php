<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('notary_requests')) {
            Schema::create('notary_requests', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('type');
                $table->string('time'); // Time field for when the request was made
                $table->string('status');
                $table->string('icon');
                $table->string('color');
                $table->timestamps();
                
                $table->index(['status', 'created_at']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('notary_requests');
    }
};
