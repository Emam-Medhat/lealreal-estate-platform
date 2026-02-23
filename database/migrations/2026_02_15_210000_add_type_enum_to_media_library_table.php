<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('media_library', function (Blueprint $table) {
            // First, drop the existing type column if it exists
            if (Schema::hasColumn('media_library', 'type')) {
                // Drop index if exists to avoid SQLite error
                try {
                    $table->dropIndex('media_library_type_uploaded_by_index');
                } catch (\Exception $e) {
                    // Index might not exist or have a different name
                }
                
                $table->dropColumn('type');
            }
        });

        Schema::table('media_library', function (Blueprint $table) {
            // Add the type column with proper enum values including 'folder'
            if (!Schema::hasColumn('media_library', 'type')) {
                $table->enum('type', ['image', 'video', 'document', 'audio', 'other', 'folder'])->default('other')->after('mime_type');
            }
        });
    }

    public function down()
    {
        Schema::table('media_library', function (Blueprint $table) {
            if (Schema::hasColumn('media_library', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
