<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warranty_requests', function (Blueprint $table) {
            $table->integer('error_code')->nullable()->comment('SAP Update Error Code');
            $table->string('status')->nullable()->comment('SAP Update Status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warranty_requests', function (Blueprint $table) {
            $table->integer('error_code')->nullable();
            $table->string('status')->nullable();
        });
    }
};
