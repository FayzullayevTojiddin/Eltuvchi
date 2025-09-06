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

            $table->string('status')->default('active');

            $table->unsignedInteger('deposit_client')->default(0);
            $table->unsignedInteger('distance_km');
            $table->unsignedInteger('price_in');
            $table->unsignedInteger('fee_per_client');

            $table->timestamps();

            $table->unique(['taxopark_from_id', 'taxopark_to_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};