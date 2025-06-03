<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::all();
        return view('dashboard.kullaniciyonetimi', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'is_active' => 'required|boolean',
        ]);

        BankAccount::create($validated);

        return response()->json(['success' => true, 'message' => 'Banka hesabı başarıyla eklendi.']);
    }

    public function edit($id)
    {
        $account = BankAccount::findOrFail($id);
        return response()->json(['success' => true, 'data' => $account]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder' => 'required|string|max:255',
            'iban' => 'required|string|max:34',
            'is_active' => 'required|boolean',
        ]);

        $account = BankAccount::findOrFail($id);
        $account->update($validated);

        return response()->json(['success' => true, 'message' => 'Banka hesabı başarıyla güncellendi.']);
    }

    public function destroy($id)
    {
        $account = BankAccount::findOrFail($id);
        $account->delete();

        return response()->json(['success' => true, 'message' => 'Banka hesabı başarıyla silindi.']);
    }
}