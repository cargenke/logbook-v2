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
        Schema::create('warranty_request_attachments', function (Blueprint $table) {
            $table->id();
            $table->integer('warranty_request_id')->nullable();
            $table->integer('logbook_id')->nullable();
            $table->string('chasisNumber')->nullable();
            $table->string('regNumber')->nullable();
            $table->string('attachmentURL')->nullable();
            $table->longText('remarks')->nullable();
            $table->timestamp('createdOn')->nullable();
            $table->integer('createdBy')->nullable()->comment('');
            $table->timestamp('editedOn')->nullable();
            $table->integer('editedBy')->nullable();
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
        Schema::dropIfExists('warranty_request_attachments');
    }
};
