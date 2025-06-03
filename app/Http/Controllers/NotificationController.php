<?php

namespace App\Http\Controllers;

use App\Services\FcmService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $user = auth()->user();
        $fcmService = new FcmService();

        $deviceTokens = $user->deviceTokens()->pluck('token')->toArray();
        foreach ($deviceTokens as $token) {
            $fcmService->sendNotification(
                $token,
                'Veresiye Bildirimi',
                'Bakiyeniz: ' . $user->veresiye()->sum('amount') . ' TL'
            );
        }

        return response()->json(['message' => 'Bildirim gönderildi']);
    }
}