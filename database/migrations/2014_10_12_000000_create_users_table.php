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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('profilePicture')->nullable();
            $table->string('coverPicture')->nullable();
            $table->integer('gender')->nullable();
            $table->integer('age')->nullable();
            $table->double('height')->nullable();
            $table->integer('weight')->nullable();
            $table->integer('weightType')->nullable();
            $table->integer('physicalActivityLevel')->nullable();
            $table->json('goals')->nullable();
            $table->timestamp('emailVerifiedAt')->nullable();
            $table->string('password')->nullable();
            $table->boolean('verified')->default(false);
            $table->string('otp')->nullable();
            $table->string('registrationType')->nullable();
            $table->string('userType')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};