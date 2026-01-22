<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\OrderStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();

            $table->decimal('passengers', 4, 2);

            $table->date('date');
            $table->time('time');

            $table->decimal('price_order', 12, 2);
            $table->decimal('client_deposit', 12, 2);
            $table->decimal('driver_payment', 12, 2)->nullable();

            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->decimal('discount_summ', 12, 2)->nullable();

            $table->string('phone', 20);
            $table->string('optional_phone', 20)->nullable();

            $table->text('note')->nullable();

            $table->enum('status', OrderStatus::values())
                ->default(OrderStatus::Created->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};