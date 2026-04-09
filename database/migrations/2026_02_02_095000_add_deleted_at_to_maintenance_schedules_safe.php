<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('maintenance_schedules', 'deleted_at')) {
            Schema::table('maintenance_schedules', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('maintenance_schedules', 'deleted_at')) {
            Schema::table('maintenance_schedules', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
