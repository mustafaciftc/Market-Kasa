<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debt_id'); // Foreign key to the debts table
            $table->decimal('amount', 10, 2); // Payment amount
            $table->date('date')->nullable(); // Date of the payment
            $table->text('description')->nullable(); // Optional description
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('debt_id')
                  ->references('id')
                  ->on('debts')
                  ->onDelete('cascade'); // Delete payments if the associated debt is deleted
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}