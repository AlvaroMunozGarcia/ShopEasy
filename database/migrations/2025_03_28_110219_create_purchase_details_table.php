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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            // Cambia 'shopping_id' a 'purchase_id'
            $table->unsignedBigInteger('purchase_id');
            // Asegúrate que la referencia también use 'purchase_id'
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade'); // Añadir onDelete es buena práctica

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // Añadir onDelete es buena práctica

            $table->integer('quantity');
            $table->decimal('price'); // Considera añadir precisión y escala, ej: decimal('price', 10, 2)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};
