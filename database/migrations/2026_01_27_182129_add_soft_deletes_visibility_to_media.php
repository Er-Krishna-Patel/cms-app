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
        Schema::table('media', function (Blueprint $table) {
            // Only add visibility - deleted_at will be added in separate migration
            if (!Schema::hasColumn('media', 'visibility')) {
                $table->enum('visibility', ['public', 'private'])->default('public')->nullable()->after('folder');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            if (Schema::hasColumn('media', 'visibility')) {
                $table->dropColumn('visibility');
            }
        });
    }
};
