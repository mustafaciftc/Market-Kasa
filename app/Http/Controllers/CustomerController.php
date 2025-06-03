<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return view('musteriyonetimi', compact('customers'));
    }

    public function create()
    {
        return view('musteriekle');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|string|email|max:255', // Updated: email format, max 255
            'address' => 'nullable|string|max:255',
        ]);

        Customer::create($request->all());

        return redirect()->route('musteriyonetimi')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

	 public function edit($id)
	{
		$customer = Customer::findOrFail($id);
		return response()->json($customer);
	}

 public function update(Request $request, $id)
{
    try {
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Müşteri ID belirtilmemiş.'
            ], 400);
        }

        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|string|email|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Müşteri başarıyla güncellendi.',
            'data' => $customer
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => "Müşteri bulunamadı: ID $id"
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Güncelleme sırasında bir hata oluştu: ' . $e->getMessage()
        ], 500);
    }
}
	
   public function destroy($id)
{
    $customer = Customer::findOrFail($id);
    $customer->delete();
    return redirect()->route('musteriyonetimi')->with('success', 'Müşteri başarıyla silindi.');
}
	
public function getBalance()
    {
        $customer = customer();
        $balance = $customer->getTotalDebtAttribute(); 
        return response()->json(['balance' => $balance]);
    }

    public function getNotifications()
    {
        $customer = customer();
        $notifications = $customer->notifications()->latest()->take(10)->get();
        return response()->json($notifications);
    }
}