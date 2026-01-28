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
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('scheduled_for')->nullable()->after('published_at');
            $table->boolean('is_pinned')->default(false)->after('scheduled_for');
            $table->integer('view_count')->default(0)->after('is_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('scheduled_for');
            $table->dropColumn('is_pinned');
            $table->dropColumn('view_count');
        });
    }
};
