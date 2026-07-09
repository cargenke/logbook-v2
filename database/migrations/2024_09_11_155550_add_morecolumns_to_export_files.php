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
        Schema::table('export_files', function (Blueprint $table) {
            $table->string('moduleName')->after('status')->nullable();
            $table->integer('createdBy')->after('moduleName')->nullable();
            $table->dateTime('createdOn')->after('createdBy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('export_files', function (Blueprint $table) {
            $table->string('moduleName')->nullable();
            $table->integer('createdBy')->nullable();
            $table->dateTime('createdOn')->nullable();
        });
    }
};
