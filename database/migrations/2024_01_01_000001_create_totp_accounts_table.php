<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('totp_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->text('secret');
            $table->integer('digits')->default(6);
            $table->integer('period')->default(30);
            $table->string('algorithm')->default('sha1');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('totp_accounts');
    }
};
