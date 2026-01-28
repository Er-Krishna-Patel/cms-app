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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('icon', 50)->nullable(); // Emoji or icon class
            $table->string('route')->nullable();
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('position')->default(0);
            $table->enum('type', ['content', 'admin', 'system'])->default('content');
            $table->string('permission')->nullable(); // Required permission/role
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable(); // Additional settings
            $table->timestamps();

            $table->index('parent_id');
            $table->index('position');
            $table->index(['is_active', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
