<?php

namespace App\Http\Controllers;

use App\Models\DebtPayment;
use Illuminate\Http\Request;

class DebtPaymentController extends Controller
{
    public function index()
    {
        return response()->json(DebtPayment::with('debt')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'debt_id' => 'required|exists:debts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|integer|in:1,2,3',
            'notes' => 'nullable|string',
        ]);

        $payment = DebtPayment::create($validated);

        return response()->json($payment, 201);
    }

    public function show(DebtPayment $debtPayment)
    {
        return response()->json($debtPayment->load('debt'));
    }

    public function update(Request $request, DebtPayment $debtPayment)
    {
        $validated = $request->validate([
            'amount' => 'sometimes|numeric|min:0.01',
            'payment_date' => 'sometimes|date',
            'payment_method' => 'sometimes|integer|in:1,2,3',
            'notes' => 'nullable|string',
        ]);

        $debtPayment->update($validated);

        return response()->json($debtPayment);
    }

    public function destroy(DebtPayment $debtPayment)
    {
        $debtPayment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
}

