<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('category', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('name'); // Category name
            $table->dateTime('created_at'); // Creation timestamp
            $table->dateTime('deleted_at')->nullable(); // Soft delete timestamp
            $table->dateTime('updated_at'); // Last update timestamp
        });
    }

    public function down()
    {
        Schema::dropIfExists('category');
    }
}