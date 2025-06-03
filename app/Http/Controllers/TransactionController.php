<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
	
public function gelirgideryonetim()
    {
        // Mevcut tarihleri belirle
        $now = Carbon::now();
        $currentMonthStart = $now->startOfMonth();
        $currentMonthEnd = $now->endOfMonth();
        $lastMonthStart = $now->subMonth()->startOfMonth();
        $lastMonthEnd = $now->subMonth()->endOfMonth();

        // Mevcut ve geçen ayın gelir-gider toplamları + tüm zamanlar
        $incomeExpenseData = [
            'currentMonthIncome' => Transaction::where('type', 1)
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->sum('price'),
            'currentMonthExpense' => Transaction::where('type', 0)
                ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
                ->sum('price'),
            'lastMonthIncome' => Transaction::where('type', 1)
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('price'),
            'lastMonthExpense' => Transaction::where('type', 0)
                ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('price'),
            'allTimeIncome' => Transaction::where('type', 1)->sum('price'),
            'allTimeExpense' => Transaction::where('type', 0)->sum('price'),
        ];

        // Tüm işlemleri getir
        $gelirgider = Transaction::with(['product', 'customer'])
            ->latest('created_at')
            ->get();

        return view('gelirgideryonetim', array_merge($incomeExpenseData, ['gelirgider' => $gelirgider]));
    }

    public function index()
    {
        $gelirgider = Transaction::orderBy('created_at')->get();
        $income = Transaction::where('type', 1)->sum('price'); // type = 1: Gelir
        $expense = Transaction::where('type', 0)->sum('price'); // type = 0: Gider
        $balance = $income - $expense;
    
        // Mevcut ayın satış toplamı
        $currentMonthTotal = Sale::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('total_price');
    
        // Geçen ayın satış toplamı
        $lastMonthTotal = Sale::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->sum('total_price');
    
        // Tüm zamanların satış toplamı
        $allTimeTotal = Sale::sum('total_price');
    
        return view('gelirgiderislem', compact(
            'gelirgider',
            'income',
            'expense',
            'balance',
            'currentMonthTotal',
            'lastMonthTotal',
            'allTimeTotal' 
        ));
    }

    public function gelirgiderislem()
    {
        // Mevcut ayın başlangıç ve bitiş tarihleri
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Geçen ayın başlangıç ve bitiş tarihleri
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Mevcut ayın gelir toplamı (type = 1 varsayıyoruz)
        $currentMonthIncome = Transaction::where('type', 1)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('price');

        // Mevcut ayın gider toplamı (type = 0 varsayıyoruz)
        $currentMonthExpense = Transaction::where('type', 0)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('price');

        // Geçen ayın gelir toplamı
        $lastMonthIncome = Transaction::where('type', 1)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('price');

        // Geçen ayın gider toplamı
        $lastMonthExpense = Transaction::where('type', 0)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('price');

        // Tüm zamanların gelir toplamı
        $allTimeIncome = Transaction::where('type', 1)->sum('price');

        // Tüm zamanların gider toplamı
        $allTimeExpense = Transaction::where('type', 0)->sum('price');

        // Tüm gelir/gider kayıtlarını al
        $Transaction = Transaction::with(['product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gelirgideryonetim', compact(
            'Transaction',
            'currentMonthIncome',
            'currentMonthExpense',
            'lastMonthIncome',
            'lastMonthExpense',
            'allTimeIncome',
            'allTimeExpense'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type' => 'required|integer|in:0,1', // 0: Gider, 1: Gelir
            'product_id' => 'nullable|exists:products,id',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        Transaction::create($validated);

        return redirect()->route('gelirgiderislem')->with('success', 'İşlem başarıyla kaydedildi.');
    }

public function update(Request $request, $id)
{
    // Validate the request data first
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'type' => 'required|integer|in:0,1',
        'product_id' => 'nullable|exists:products,id',
        'customer_id' => 'nullable|exists:customers,id',
    ]);

    try {
        // Find the transaction or fail
        $transaction = Transaction::findOrFail($id);
        
        // Update the transaction
        $transaction->update($validated);

        return redirect()
            ->route('gelirgiderislem')
            ->with('success', 'İşlem başarıyla güncellendi.');

    } catch (ModelNotFoundException $e) {
        return redirect()
            ->route('gelirgiderislem')
            ->with('error', 'Güncellenmek istenen işlem kaydı bulunamadı.');
            
    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error('Transaction update error: ' . $e->getMessage());
        
        return redirect()
            ->route('gelirgiderislem')
            ->with('error', 'İşlem güncellenirken bir hata oluştu. Lütfen tekrar deneyin.');
    }
}
	   
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->route('gelirgiderislem')->with('success', 'İşlem başarıyla silindi.');
    }
}