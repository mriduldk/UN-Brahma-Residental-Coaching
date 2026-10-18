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
        if (!Schema::hasTable('admit_card_download_logs')) {
            Schema::create('admit_card_download_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_application_id')
                    ->constrained('student_applications')
                    ->onDelete('cascade');
                $table->string('application_code')->nullable()->index();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('user_name'); // Who is downloading (name)
                $table->string('user_type'); // 'student' or 'admin'
                $table->string('ip_address', 45); // IPv4 or IPv6
                $table->text('user_agent')->nullable();
                $table->string('download_type')->default('single_pdf'); // single_pdf, bulk_pdf, web_print
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('set null');
                $table->timestamp('downloaded_at')->useCurrent()->index();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admit_card_download_logs');
    }
};
