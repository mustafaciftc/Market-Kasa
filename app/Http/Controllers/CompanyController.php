<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return view('firmayonetimi', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        Company::create($request->all());
        return redirect()->route('firmayonetimi')->with('success', 'Firma başarıyla oluşturuldu.');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string'
        ]);

        $company->update($request->all());
        return redirect()->route('firmayonetimi')->with('success', 'Firma başarıyla güncellendi.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('firmayonetimi')->with('success', 'Firma başarıyla silindi.');
    }
}

