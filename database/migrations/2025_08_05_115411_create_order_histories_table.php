<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            $table->enum('status', \App\Enums\OrderStatus::values());
            
            $table->foreignId('changed_by_id')->nullable(); // Client yoki Driver
            $table->string('changed_by_type')->nullable(); // 'client' | 'driver' | 'system'

            $table->text('description')->nullable(); // Misol: "Client cancelled before acceptance", "Payment transferred to driver", va hokazo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_histories');
    }
};