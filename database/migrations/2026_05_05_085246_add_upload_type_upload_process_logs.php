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
        Schema::table('upload_process_logs', function (Blueprint $table) {
                   $table->integer('process_type')->default(0)->comment("0=Bulk Upload Request, 1=Update Request,8=Direct Trasfer Upload,");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upload_process_logs', function (Blueprint $table) {
            $table->dropColumn('process_type');
        });
    }
};
