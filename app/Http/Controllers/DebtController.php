<?php

namespace App\Http\Controllers;

use App\Mail\DebtReminderMail;
use App\Models\Debt;
use App\Models\Customer;
use App\Models\SaleStatistic;
use App\Models\Payment;
use App\Models\DebtReminder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class DebtController extends Controller
{
    public function index()
    {
        $debts = Debt::with('customer')->get();
        $customers = Customer::all();
        return view('veresiyeyonetimi', compact('debts', 'customers'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('veresiyeekle', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'term' => 'nullable|integer|min:0',
        ]);

        $debt = Debt::create($request->all());
        return response()->json(['success' => true, 'message' => 'Veresiye eklendi.', 'debt' => $debt], 201);
    }

    public function show($id)
    {
        $debt = Debt::with(['customer', 'payments', 'sale'])->findOrFail($id);
        return view('veresiyedetay', compact('debt'));
    }

    public function remind(Request $request)
    {
        $validated = $request->validate([
            'debt_id' => 'required|exists:debts,id',
            'reminder_type' => 'required',
            'notes' => 'nullable|string|max:500',
            'reminder_date' => 'nullable|date|after_or_equal:today',
        ]);

        try {
            $debt = Debt::with('customer')->findOrFail($validated['debt_id']);
            $customer = $debt->customer;

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu borç kaydına ait müşteri bulunamadı.'
                ], 404);
            }

            // Vade tarihini hesapla ve zamanı sıfırla
            $debtDate = $debt->date ?? $debt->created_at ?? now();
            $debtTerm = max(0, $debt->term ?? 30);
            $dueDate = Carbon::parse($debtDate)->startOfDay()->addDays($debtTerm);

            // Hatırlatma mesajı için gün farkını hesapla
            $daysUntilDue = (int) now()->startOfDay()->diffInDays($dueDate, false);
            $reminderMessage = $this->generateReminderMessage($debt, $daysUntilDue, $validated['notes'] ?? null);

            // Hatırlatma kaydı oluştur
            $reminderData = [
                'debt_id' => $debt->id,
                'customer_id' => $customer->id,
                'reminder_type' => 'email',
                'message' => $reminderMessage,
                'reminder_date' => $validated['reminder_date'] ?? now(),
                'status' => 'pending',
                'due_date' => $dueDate,
                'notes' => $validated['notes'] ?? null,
            ];

            if (!isset($reminderData['due_date']) || is_null($reminderData['due_date'])) {
                throw new \Exception('due_date değeri sağlanamadı.');
            }

            $reminder = DebtReminder::create($reminderData);

            // E-posta hatırlatması gönder
            if ($customer->email) {
                try {
                    Mail::to($customer->email)->send(new DebtReminderMail($reminder, $debt));
                    $reminder->update(['status' => 'sent']);

                    return response()->json([
                        'success' => true,
                        'message' => 'E-posta hatırlatması başarıyla gönderildi.'
                    ]);
                } catch (\Exception $e) {
                    Log::error('E-posta hatırlatma hatası: ' . $e->getMessage());
                    $reminder->update(['status' => 'failed']);

                    return response()->json([
                        'success' => false,
                        'message' => 'E-posta gönderilirken hata oluştu: ' . $e->getMessage()
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Müşteri e-posta adresi bulunamadı.'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Hatırlatma hatası: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Hatırlatma oluşturulurken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateReminderMessage($debt, $daysUntilDue, $customNotes = null)
    {
        $customerName = $debt->customer->name ?? 'Müşteri';
        $amount = number_format($debt->amount, 2, ',', '.');
        $dueDateFormatted = $debt->date && is_numeric($debt->term)
            ? Carbon::parse($debt->date)->startOfDay()->addDays($debt->term)->format('d.m.Y')
            : 'Belirtilmemiş';

        $message = "Sayın $customerName,\n\n";

        if ($daysUntilDue !== null) {
            if ($daysUntilDue > 0) {
                $message .= "$daysUntilDue gün sonra ($dueDateFormatted) ödeme yapmanız gereken $amount ₺ tutarında borcunuz bulunmaktadır.\n";
            } elseif ($daysUntilDue == 0) {
                $message .= "Bugün ($dueDateFormatted) ödeme yapmanız gereken $amount ₺ tutarında borcunuz bulunmaktadır.\n";
            } else {
                $message .= abs($daysUntilDue) . " gün önce ($dueDateFormatted) ödeme yapmanız gereken $amount ₺ tutarında borcunuz bulunmaktadır.\n";
            }
        } else {
            $message .= "Ödeme tarihiniz: $dueDateFormatted. $amount ₺ tutarında borcunuz bulunmaktadır.\n";
        }

        if ($customNotes) {
            $message .= "\nNot: $customNotes\n";
        }

        $message .= "\nBizi tercih ettiğiniz için teşekkür ederiz.\n";

        return $message;
    }

    public function sendAutoReminders()
    {
        // Önceden tanımlanmış hatırlatma günleri
        $reminderDays = [0, 3, 7, 25, 30]; // Bugün, 3 gün, 7 gün, 25 gün, 30 gün
        $results = [];

        // Vadesi yaklaşan borçları al
        $upcomingDebts = Debt::where('is_fully_paid', false)
            ->whereHas('customer', function($query) {
                $query->whereNotNull('email')->orWhereNotNull('phone');
            })
            ->with(['customer', 'reminders'])
            ->get()
            ->filter(function($debt) use ($reminderDays) {
                $dueDate = Carbon::parse($debt->date)->startOfDay()->addDays($debt->term);
                $daysUntilDue = (int) now()->startOfDay()->diffInDays($dueDate, false);
                return in_array($daysUntilDue, $reminderDays);
            });

        foreach ($upcomingDebts as $debt) {
            $dueDate = Carbon::parse($debt->date)->startOfDay()->addDays($debt->term);
            $daysUntilDue = (int) now()->startOfDay()->diffInDays($dueDate, false);

            // Son 7 gün içinde hatırlatma gönderilmiş mi kontrol et
            $recentReminder = $debt->reminders()
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if (!$recentReminder) {
                $reminderType = ($debt->customer->email && $debt->customer->phone) ? 'both' :
                               ($debt->customer->email ? 'email' : 'sms');

                $result = $this->remind(new Request([
                    'debt_id' => $debt->id,
                    'reminder_type' => $reminderType,
                    'notes' => 'Otomatik hatırlatma: Ödeme tarihi yaklaşıyor.',
                ]));

                $results[] = $result->getData(true);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Otomatik hatırlatmalar gönderildi.',
            'results' => $results,
        ]);
    }

    public function edit($id)
    {
        $debt = Debt::findOrFail($id);
        return response()->json($debt);
    }

    public function update(Request $request, $id)
    {
        $debt = Debt::findOrFail($id);
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'term' => 'nullable|integer|min:0',
        ]);

        $debt->update($request->all());
        return response()->json(['success' => true, 'message' => 'Veresiye güncellendi.', 'debt' => $debt]);
    }

    public function destroy($id)
    {
        try {
            $debt = Debt::findOrFail($id);

            Log::info("Borç Silindi: {$debt->id}", [
                'customer_id' => $debt->customer_id,
                'amount' => $debt->amount,
                'description' => $debt->description,
            ]);

            $debt->payments()->delete();
            $debt->delete();

            return redirect()->route('veresiyeyonetimi')->with('success', 'Borç başarıyla silindi.');
        } catch (\Exception $e) {
            Log::error('Silme hatası: ' . $e->getMessage());
            return redirect()->route('veresiyeyonetimi')->with('error', 'Borç silinirken hata oluştu.');
        }
    }
}
