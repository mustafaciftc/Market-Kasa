<?php

if (!function_exists('customer')) {
    function customer() {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception('Oturumda kullanıcı bulunamadı.');
        }

        $customer = \App\Models\Customer::where('email', $user->email)->first();
        if (!$customer) {
            throw new \Exception('Bu kullanıcıya bağlı müşteri kaydı bulunamadı.');
        }

        return $customer;
    }
}