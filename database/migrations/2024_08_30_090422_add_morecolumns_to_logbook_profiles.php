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
        Schema::table('logbook_profiles', function (Blueprint $table) {
            $table->integer('isAllocated')->nullable()->comment('0=>No,1=>Yes, For registration');
            $table->integer('allocatedBy')->nullable();
            $table->dateTime('allocatedOn')->nullable();
            $table->integer('isAvailable')->nullable()->comment('0=>No,1=>Yes,For Logbook');
            $table->integer('isReturned')->nullable()->comment('0=>No,1=>Yes,For Credit Notes Done	');
            $table->integer('returnedBy')->nullable();
            $table->dateTime('returnedOn')->nullable();
            $table->dateTime('packingListCreatedOn')->nullable();
            $table->integer('packingListCreatedBy')->nullable();
            $table->dateTime('allocationsCreatedOn')->nullable();
            $table->integer('allocationsCreatedBy')->nullable();
            $table->dateTime('pendingRequestsCreatedOn')->nullable();
            $table->integer('pendingRequestsCreatedBy')->nullable();
            $table->dateTime('requestsCreatedOn')->nullable();
            $table->integer('requestsCreatedBy')->nullable();
            $table->dateTime('pendingAcceptanceCreatedOn')->nullable();
            $table->integer('pendingAcceptanceCreatedBy')->nullable();
            $table->dateTime('acceptanceCreatedOn')->nullable();
            $table->integer('acceptanceCreatedBy')->nullable();
            $table->dateTime('issuesCreatedOn')->nullable();
            $table->integer('issuesCreatedBy')->nullable();
            $table->dateTime('dispatchCreatedOn')->nullable();
            $table->integer('dispatchCreatedBy')->nullable();
            $table->string('dispatchedDate')->nullable();
            $table->string('dispatchedBy')->nullable();
            $table->string('dispatchedTo')->nullable();
            $table->string('dispatchedYear')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logbook_profiles', function (Blueprint $table) {
            $table->integer('isAllocated')->nullable();
            $table->integer('allocatedBy')->nullable();
            $table->dateTime('allocatedOn')->nullable();
            $table->integer('isAvailable')->nullable();
            $table->integer('isReturned')->nullable();
            $table->integer('returnedBy')->nullable();
            $table->dateTime('returnedOn')->nullable();
            $table->dateTime('packingListCreatedOn')->nullable();
            $table->integer('packingListCreatedBy')->nullable();
            $table->dateTime('allocationsCreatedOn')->nullable();
            $table->integer('allocationsCreatedBy')->nullable();
            $table->dateTime('pendingRequestsCreatedOn')->nullable();
            $table->integer('pendingRequestsCreatedBy')->nullable();
            $table->dateTime('requestsCreatedOn')->nullable();
            $table->integer('requestsCreatedBy')->nullable();
            $table->dateTime('pendingAcceptanceCreatedOn')->nullable();
            $table->integer('pendingAcceptanceCreatedBy')->nullable();
            $table->dateTime('acceptanceCreatedOn')->nullable();
            $table->integer('acceptanceCreatedBy')->nullable();
            $table->dateTime('issuesCreatedOn')->nullable();
            $table->integer('issuesCreatedBy')->nullable();
            $table->dateTime('dispatchCreatedOn')->nullable();
            $table->integer('dispatchCreatedBy')->nullable();
            $table->string('dispatchedDate')->nullable();
            $table->string('dispatchedBy')->nullable();
            $table->string('dispatchedTo')->nullable();
            $table->string('dispatchedYear')->nullable();
        });
    }
};
