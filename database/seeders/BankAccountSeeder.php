<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    public function run()
    {
        BankAccount::create([
            'bank_name' => 'Örnek Banka',
            'account_holder' => 'Şirket Adı',
            'iban' => 'TR12 3456 7890 1234 5678 9012 34',
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => 'Başka Banka',
            'account_holder' => 'Şirket Adı',
            'iban' => 'TR98 7654 3210 9876 5432 1098 76',
            'is_active' => true,
        ]);
    }
}
