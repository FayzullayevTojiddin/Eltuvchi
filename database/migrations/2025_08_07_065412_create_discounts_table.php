<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('value')->nullable();
            $table->integer('points')->nullable();
            $table->string('title')->nullable();
            $table->string('icon')->nullable();
            $table->integer('percent')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};