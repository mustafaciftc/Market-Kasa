<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTermToDebtsTable extends Migration
{
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->integer('term')->nullable()->after('description');
        });

        // Set a default value for existing records
        \DB::table('debts')->whereNull('term')->update(['term' => 30]);
    }

    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn('term');
        });
    }
}