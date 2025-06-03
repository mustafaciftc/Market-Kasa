<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = Staff::all();
        return view('personelyonetimi', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ]);

        $staff = Staff::create($request->all());
        return response()->json(['success' => true, 'message' => 'Personel eklendi.', 'staff' => $staff], 201);
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        return response()->json([
            'id' => $staff->id,
            'name' => $staff->name,
            'email' => $staff->email,
            'phone' => $staff->phone,
            'position' => $staff->position,
            'salary' => $staff->salary,
            'hire_date' => $staff->hire_date->format('Y-m-d'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ]);

        $staff->update($request->all());
        return response()->json(['success' => true, 'message' => 'Personel güncellendi.', 'staff' => $staff]);
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();
        return redirect()->route('personelyonetimi')->with('success', 'Personel silindi.');
    }
}
