<?php

use App\Enums\RouteStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('taxopark_from_id')
                ->constrained('taxo_parks')
                ->cascadeOnDelete();

            $table->foreignId('taxopark_to_id')
                ->constrained('taxo_parks')
                ->cascadeOnDelete();

            $table->string('status')->default(RouteStatus::ACTIVE->value);

            $table->unsignedInteger('deposit_client')->default(0); // mijoz oldindan to‘lashi kerak
            $table->unsignedInteger('distance_km');                // masofa
            $table->unsignedInteger('price_in');                   // mijozdan olinadigan pul
            $table->unsignedInteger('fee_per_client');             // taksidan olinadigan pul (1 mijoz uchun)

            $table->timestamps();

            $table->unique(['taxopark_from_id', 'taxopark_to_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};