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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->nullable();
            $table->string('staff_no')->nullable();
            $table->string('name')->nullable();
            $table->string('pin_no')->nullable();
            $table->string('card_code')->nullable();
            $table->string('card_name')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('google_id')->nullable();
            $table->string('password')->nullable();
            $table->integer('UserType')->default(0)->comment('0 = Normal User, 1 = Super Admin');
            $table->string('tel')->nullable();
            $table->integer('directory')->nullable();
            $table->string('job_position')->nullable();
            $table->string('location')->nullable();
            $table->string('attachment')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
