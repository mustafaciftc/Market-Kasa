<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaleIdToSaleStatisticTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sale_statistic', function (Blueprint $table) {
            // sale_id sütununu ekle
            $table->unsignedBigInteger('sale_id')->nullable()->after('id');

            // Foreign key tanımla
            $table->foreign('sale_id')
                  ->references('id')
                  ->on('sales')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_statistic', function (Blueprint $table) {
            // Foreign key'i kaldır
            $table->dropForeign(['sale_id']);

            // sale_id sütununu kaldır
            $table->dropColumn('sale_id');
        });
    }
}