<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $forms = DB::table('application_forms')->get();
        foreach ($forms as $form) {
            if ($form->application_code) {
                DB::table('form_links')->insert([
                    'application_form_id' => $form->id,
                    'link_code' => $form->application_code,
                    'description' => 'Default Link',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            //
        });
    }
};
