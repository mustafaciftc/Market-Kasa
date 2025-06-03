<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
 public function index()
{
    return view('ayarlar');
}

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'vergi_number' => 'nullable|string|max:50',
            'vergi_dairesi' => 'nullable|string|max:255',
            'light_logo' => 'nullable|image|max:2048',
            'dark_logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:2048',
            'perm_option' => 'nullable|in:1,2,12,13',
            'register_module' => 'nullable|in:0,1',
            'theme' => 'nullable|in:light,dark',
        ]);

        $settings = [];

        // Firma Ayarları
        if ($request->has('name')) $settings['name'] = $request->input('name');
        if ($request->has('phone')) $settings['phone'] = $request->input('phone');
        if ($request->has('email')) $settings['email'] = $request->input('email');
        if ($request->has('address')) $settings['address'] = $request->input('address');
        if ($request->has('website')) $settings['website'] = $request->input('website');
        if ($request->has('vergi_number')) $settings['vergi_number'] = $request->input('vergi_number');
        if ($request->has('vergi_dairesi')) $settings['vergi_dairesi'] = $request->input('vergi_dairesi');

        // Görsel Ayarları
        if ($request->hasFile('light_logo')) {
            $settings['light_logo'] = $request->file('light_logo')->store('settings', 'public');
        }
        if ($request->hasFile('dark_logo')) {
            $settings['dark_logo'] = $request->file('dark_logo')->store('settings', 'public');
        }
        if ($request->hasFile('favicon')) {
            $settings['favicon'] = $request->file('favicon')->store('settings', 'public');
        }

        // Diğer Ayarlar
        if ($request->has('perm_option')) $settings['perm_option'] = $request->input('perm_option');
        if ($request->has('register_module')) $settings['register_module'] = $request->input('register_module');
        if ($request->has('theme')) $settings['theme'] = $request->input('theme');

        // Ayarları kaydetme (örneğin bir Settings modeline veya config dosyasına)
        foreach ($settings as $key => $value) {
            // Örnek: Veritabanına kaydetme (Settings modeli varsa)
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['success' => true, 'message' => 'Ayarlar güncellendi.']);
    }
}