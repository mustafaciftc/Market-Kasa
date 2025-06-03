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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable();
            $table->string('perm')->nullable();
            $table->boolean('demo')->default(false);
            $table->boolean('admin')->default(false);
            $table->string('phone')->nullable();
            $table->boolean('active')->default(true);
            $table->string('website')->nullable();
            $table->string('company')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'perm',
                'demo',
                'admin',
                'phone',
                'active',
                'website',
                'company',
            ]);
        });
    }
};