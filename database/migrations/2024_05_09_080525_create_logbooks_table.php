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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->string('chasisNumber')->nullable();
            $table->string('regNumber')->nullable();
            $table->string('status')->nullable();
            $table->integer('isAllocated')->default(0)->comment('0=>No,1=>Yes, For registration');
            $table->integer('allocatedBy')->nullable()->comment('Staff who did registered');
            $table->dateTime('allocatedOn')->nullable()->comment('Date Registered');
            $table->integer('isAvailable')->default(0)->comment('0=>No,1=>Yes,For Logbook');
            $table->integer('isReturned')->default(0)->comment('0=>No,1=>Yes,For Credit Notes Done');
            $table->dateTime('createdOn')->nullable()->comment('date synced');
            $table->string('createdBy')->nullable();
            $table->dateTime('packingListCreatedOn')->nullable()->comment('date packing lists was created');
            $table->string('packingListCreatedBy')->nullable();
            $table->dateTime('allocationsCreatedOn')->nullable()->comment('date allocated');
            $table->string('allocationsCreatedBy')->nullable();
            $table->dateTime('pendingRequestsCreatedOn')->nullable()->comment('date synced from SAP');
            $table->string('pendingRequestsCreatedBy')->nullable();
            $table->dateTime('requestsCreatedOn')->nullable()->comment('');
            $table->string('requestsCreatedBy')->nullable();
            $table->dateTime('pendingAcceptanceCreatedOn')->nullable()->comment('');
            $table->string('pendingAcceptanceCreatedBy')->nullable();
            $table->dateTime('acceptanceCreatedOn')->nullable()->comment('');
            $table->string('acceptanceCreatedBy')->nullable();
            $table->dateTime('issuesCreatedOn')->nullable()->comment('');
            $table->string('issuesCreatedBy')->nullable();
            $table->dateTime('dispatchCreatedOn')->nullable()->comment('');
            $table->string('dispatchCreatedBy')->nullable();
            $table->dateTime('editedOn')->nullable();
            $table->string('editedBy')->nullable();
            $table->string('dispatchedDate')->nullable();
            $table->string('dispatchedBy')->nullable();
            $table->string('dispatchedTo')->nullable();
            $table->string('dispatchedYear')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('logbooks');
    }
};
