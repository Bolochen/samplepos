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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions', 'id')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menus', 'id')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->enum('kitchen_status', ['pending', 'preparing', 'ready'])->default('pending');
            $table->enum('serving_status', ['pending', 'served'])->default('pending');
            $table->enum('type', ['order', 'preorder'])->default('preorder');
            $table->string('notes')->nullable();
            $table->foreignId('bill_id')->nullable()->constrained('bills', 'id')->onDelete('set null');
            $table->timestamps();
            $table->index(['transaction_id', 'bill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
