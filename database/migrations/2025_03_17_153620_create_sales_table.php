<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->json('basket'); // JSON column for product details
            $table->unsignedBigInteger('customer_id'); // Foreign key for customer
            $table->decimal('discount', 10, 2)->default(0.00); // Discount amount
            $table->decimal('sub_total', 10, 2); // Subtotal before discount
            $table->decimal('total_price', 10, 2); // Final total price
            $table->integer('pay_type'); // Payment type (e.g., 1 for cash, 2 for card)
            $table->decimal('discount_total', 10, 2)->default(0.00); // Total discount applied
            $table->timestamps(); // created_at and updated_at

            // Foreign key constraint (assuming a customers table exists)
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
}
