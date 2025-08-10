<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('score');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'client_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_reviews');
    }
};