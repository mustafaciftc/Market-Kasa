<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->nullable();
            $table->string('phone', 60)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('vergi_number', 255)->nullable();
            $table->string('vergi_dairesi', 255)->nullable();
            $table->string('light_logo', 255)->nullable();
            $table->string('dark_logo', 255)->nullable();
            $table->string('favicon', 255)->nullable();
            $table->string('perm_option', 255)->nullable();
            $table->integer('register_module')->nullable();
            $table->string('theme', 20)->nullable();
            $table->string('menu', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}