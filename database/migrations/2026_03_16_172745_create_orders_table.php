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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_number')->unique(); 
        $table->string('customer_number');
        $table->string('customer_name');
        $table->text('fiscal_data'); 
        $table->text('delivery_address');
        $table->text('notes')->nullable();

        $table->enum('status', ['Ordered', 'In process', 'In route', 'Delivered'])
              ->default('Ordered');

        $table->boolean('is_deleted')->default(false); 

        $table->foreignId('user_id')->constrained('users');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
