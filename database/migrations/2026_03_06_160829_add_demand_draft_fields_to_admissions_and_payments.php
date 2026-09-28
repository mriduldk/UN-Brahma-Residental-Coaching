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
        Schema::table('admissions', function (Blueprint $table) {
            $table->boolean('allow_dd_full_payment')->default(false)->after('cash_payment_days');
            $table->decimal('dd_full_payment_discount_amount', 10, 2)->nullable()->default(0)->after('allow_dd_full_payment');
            $table->decimal('dd_full_payment_discount_percentage', 5, 2)->nullable()->default(0)->after('dd_full_payment_discount_amount');
            
            $table->boolean('allow_dd_emi')->default(false)->after('dd_full_payment_discount_percentage');
            $table->json('dd_emi_tenures')->nullable()->after('allow_dd_emi');
            $table->decimal('dd_emi_down_payment', 10, 2)->nullable()->after('dd_emi_tenures');
        });

        Schema::table('admission_payments', function (Blueprint $table) {
            $table->string('dd_number')->nullable()->after('transaction_id');
            $table->date('dd_date')->nullable()->after('dd_number');
            $table->string('dd_bank')->nullable()->after('dd_date');
            $table->string('dd_attachment_path')->nullable()->after('dd_bank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropColumn([
                'allow_dd_full_payment',
                'dd_full_payment_discount_amount',
                'dd_full_payment_discount_percentage',
                'allow_dd_emi',
                'dd_emi_tenures',
                'dd_emi_down_payment'
            ]);
        });

        Schema::table('admission_payments', function (Blueprint $table) {
            $table->dropColumn([
                'dd_number',
                'dd_date',
                'dd_bank',
                'dd_attachment_path'
            ]);
        });
    }
};
