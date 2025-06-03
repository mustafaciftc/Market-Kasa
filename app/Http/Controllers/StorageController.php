<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class StorageController extends Controller
{
    public function createStorageLink()
    {
        try {
            Artisan::call('storage:link');
            return response()->json(['message' => 'Storage link created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create storage link: ' . $e->getMessage()], 500);
        }
    }
}