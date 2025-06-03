<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Debt;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('debt')->get();
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $debts = Debt::all();
        return view('payments.create', compact('debts'));
    }

    public function store(Request $request)
    {
        Payment::create($request->all());
        return redirect()->route('payments.index');
    }

    // Diğer CRUD metodları...
}