<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDebtRemindersTable extends Migration
{
    public function up()
    {
        Schema::create('debt_reminders', function (Blueprint $table) {
            $table->id(); // id alanı otomatik olarak PRIMARY KEY ve AUTO_INCREMENT olacaktır.
            $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade'); // debt_id sütunu, debts tablosunun id'sine referans verir.
            $table->dateTime('reminder_date'); // Hatırlatma tarihi
            $table->tinyInteger('reminder_type'); // Hatırlatma türü (1: SMS, 2: E-posta, 3: Telefon, 4: Diğer)
            $table->tinyInteger('status')->default(0); // Durum (0: Beklemede, 1: Gönderildi, 2: Başarısız) varsayılan 0
            $table->text('notes')->nullable(); // Notlar (opsiyonel)
            $table->timestamps(); // created_at ve updated_at sütunları
        });
    }

    public function down()
    {
        Schema::dropIfExists('debt_reminders'); // debt_reminders tablosunu siler
    }
}
