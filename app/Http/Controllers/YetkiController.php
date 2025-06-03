<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class YetkiController extends Controller
{
    // Liste sayfası
    public function index()
    {
        $permissions = Permission::all(); 
        return view('yetki', compact('permissions'));
    }

    // Ekleme formu
    public function create()
    {
        return view('yetkiekle');
    }

    // Ekleme işlemi
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);

        Permission::create([
            'name' => $request->name,
        ]);

        return redirect()->route('yetki')->with('success', 'Yetki başarıyla eklendi.');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('yetkiedit', compact('permission'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$id,
        ]);
    
        $permission = Permission::findOrFail($id);
        $permission->update([
            'name' => $request->name,
        ]);
    
        return redirect()->route('yetki')->with('success', 'Yetki başarıyla güncellendi.');
    }

    // Silme işlemi
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('yetki')->with('success', 'Yetki başarıyla silindi.');
    }
}