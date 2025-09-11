<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pointable_id');
            $table->string('pointable_type');
            $table->integer('points');
            $table->enum('type', ['plus', 'minus']);
            $table->integer('points_after');
            $table->string('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['pointable_id', 'pointable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};