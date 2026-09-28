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
        Schema::create('school_feed_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_id')->constrained('school_feeds')->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained('school_feed_poll_options')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['feed_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_feed_poll_votes');
    }
};
