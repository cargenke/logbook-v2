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
        Schema::table('main_mf', function (Blueprint $table) {
            if (! Schema::hasColumn('main_mf', 'created_by')) {
                $table->string('created_by')->nullable(); // registered by
            }
            if (! Schema::hasColumn('main_mf', 'invoice_no')) {
                $table->integer('invoice_no')->nullable();
            }
            if (! Schema::hasColumn('main_mf', 'color')) {
                $table->string('color')->nullable();
            }
            if (! Schema::hasColumn('main_mf', 'received_on')) {
                $table->string('received_on')->nullable();
            }
            if (! Schema::hasColumn('main_mf', 'entry_no')) {
                $table->string('entry_no')->nullable();
            }
            if (! Schema::hasColumn('main_mf', 'PinNo')) {
                $table->string('PinNo')->nullable();
            }
            if (! Schema::hasColumn('main_mf', 'registered_owner')) {
                $table->string('registered_owner')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('main_mf', function (Blueprint $table) {
            //
        });
    }
};
