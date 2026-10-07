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
        Schema::create('wedding_music', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('category', 80);
            $table->string('youtube_url');
            $table->string('youtube_video_id', 11);
            $table->string('thumbnail');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wedding_music');
    }
};
