<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxo_parks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxo_parks');
    }
};