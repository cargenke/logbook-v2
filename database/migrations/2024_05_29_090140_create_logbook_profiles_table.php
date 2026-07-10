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
        Schema::create('logbook_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('logbook_id')->nullable();
            $table->string('chasisNumber')->nullable();
            $table->string('regNumber')->nullable();
            $table->string('CardCode')->nullable();
            $table->string('CardName')->nullable()->comment('Bp Name');
            $table->string('CustomerName')->nullable()->comment('Customer');
            $table->string('DocNum')->nullable();
            $table->string('Location')->nullable();
            $table->string('PinNo')->nullable();
            $table->string('IDNo')->nullable();
            $table->dateTime('TransFerDate')->nullable();
            $table->string('DocDate')->nullable();
            $table->string('NumAtCard')->nullable();
            $table->string('tel')->nullable();
            $table->decimal('LogBookFee', 20, 2)->nullable();
            $table->string('U_ProdLine')->nullable();
            $table->string('EngineNumber')->nullable();
            $table->string('groupCode')->nullable();
            $table->string('groupName')->nullable();
            $table->dateTime('createdOn')->nullable();
            $table->string('createdBy')->nullable();
            $table->dateTime('editedOn')->nullable();
            $table->string('editedBy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logbook_profiles');
    }
};
