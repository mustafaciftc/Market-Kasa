<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->date('entry_date')->nullable(); 
            $table->date('expiration_date')->nullable(); 
            $table->integer('stock_quantity')->default(0); 
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['entry_date', 'expiration_date', 'stock_quantity']);
        });
    }
};
