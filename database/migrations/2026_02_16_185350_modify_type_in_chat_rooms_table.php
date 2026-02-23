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
        // Modify the enum to include 'public'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE chat_rooms MODIFY COLUMN type ENUM('general', 'property', 'support', 'private', 'public') DEFAULT 'general'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values (be careful as this might truncate 'public' values if any exist)
        // For safety in development we can leave 'public' or try to convert back if needed.
        // Ideally we should map 'public' back to 'general' before reverting, but for now we just revert definition.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("UPDATE chat_rooms SET type = 'general' WHERE type = 'public'");
            DB::statement("ALTER TABLE chat_rooms MODIFY COLUMN type ENUM('general', 'property', 'support', 'private') DEFAULT 'general'");
        }
    }
};
