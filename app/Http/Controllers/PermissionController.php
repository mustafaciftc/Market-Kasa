<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return view('yetkiislemleri', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create($request->all());
        return response()->json(['success' => true, 'message' => 'Yetki eklendi.', 'permission' => $permission], 201);
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json([
            'id' => $permission->id,
            'name' => $permission->name,
        ]);
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        $permission->update($request->all());
        return response()->json(['success' => true, 'message' => 'Yetki güncellendi.', 'permission' => $permission]);
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return redirect()->route('yetkiislemleri')->with('success', 'Yetki silindi.');
    }
}